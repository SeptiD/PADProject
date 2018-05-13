
import com.neovisionaries.i18n.CountryCode;
import kafka.utils.Json;
import org.apache.kafka.clients.consumer.*;

import org.apache.kafka.clients.consumer.Consumer;

import org.apache.kafka.common.serialization.LongDeserializer;

import org.apache.kafka.common.serialization.StringDeserializer;
import org.json.*;


import java.sql.*;


import java.util.*;

public class TwitterKafkaConsumer {
    private static int counter=0;
    private static HashSet<CharSequence> sports = new HashSet<CharSequence>();
    private static HashMap<CharSequence,Integer> sportsCount = new HashMap<CharSequence, Integer>();
    private static CharSequence mlsc="#MLS";
    private static CharSequence nbac="#NBA";
    private static CharSequence mlbc="#MLB";
    private static CharSequence nhlc="#NHL";
    private static CharSequence nflc="#NFL";
    private static Connection conn;


    private final static String TOPIC = "sports-topic";

    private final static String BOOTSTRAP_SERVERS =

            "localhost:9092,localhost:9093,localhost:9094";
    private static Consumer<Long, String> createConsumer() {

        final Properties props = new Properties();

        props.put(ConsumerConfig.BOOTSTRAP_SERVERS_CONFIG,

                BOOTSTRAP_SERVERS);

        props.put(ConsumerConfig.GROUP_ID_CONFIG,

                "KafkaExampleConsumer");

        props.put(ConsumerConfig.KEY_DESERIALIZER_CLASS_CONFIG,

                LongDeserializer.class.getName());

        props.put(ConsumerConfig.VALUE_DESERIALIZER_CLASS_CONFIG,

                StringDeserializer.class.getName());

        // Create the consumer using props.

        final Consumer<Long, String> consumer =

                new KafkaConsumer<>(props);

        // Subscribe to the topic.

        consumer.subscribe(Collections.singletonList(TOPIC));

        return consumer;

    }
    static void runConsumer() throws InterruptedException {
        sports.add(mlsc);
        sports.add(nbac);
        sports.add(mlbc);
        sports.add(nhlc);
        sports.add(nflc);
        sportsCount.put(mlsc,0);
        sportsCount.put(nbac,0);
        sportsCount.put(mlbc,0);
        sportsCount.put(nhlc,0);
        sportsCount.put(nflc,0);

        final Consumer<Long, String> consumer = createConsumer();

        final int giveUp = 10000;   int noRecordsCount = 0;

        while (true) {

            final ConsumerRecords<Long, String> consumerRecords =

                    consumer.poll(1000);

            if (consumerRecords.count()==0) {

                noRecordsCount++;

                if (noRecordsCount > giveUp) break;

                else continue;

            }

            consumerRecords.forEach(record -> {

                System.out.printf("Consumer Record:(%d, %s, %d, %d)\n",

                        record.key(), record.value(),

                        record.partition(), record.offset());
                for (CharSequence sport:sports) {
                    if(record.value().contains(sport))
                    {
                        Integer temp = sportsCount.get(sport);
                        sportsCount.replace(sport,temp+1);
                        counter++;
                    }
                }
                if(counter > 10){
                    sendToDatabase();
                }
                checkcountry_code(record);

            });

            consumer.commitAsync();

        }

        consumer.close();

        System.out.println("DONE");

    }
    private static void checkcountry_code(ConsumerRecord<Long,String> record) {
        JSONObject myJsonObj = new JSONObject(record.value());
        try {
            String countryCode = myJsonObj.getJSONObject("place").getString("country_code");
            countryCode = CountryCode.getByCode(countryCode).getAlpha3();
            System.out.println("Found a country:"+countryCode);
            sendToCountryDatabase(countryCode);
        }catch (JSONException e) {}
    }

    private static void sendToCountryDatabase(String countryCode) {
        String query ="SELECT * FROM countriesdata where country = '"+countryCode+"'";
        try {
            Statement st = conn.createStatement();
            ResultSet rs = st.executeQuery(query);
            PreparedStatement updateEXP;
            int counter;
            if(rs.next()){
                //there is an entrance in the database.we update it with counter+1
                counter = rs.getInt("counter");
                counter++;
                updateEXP = conn.prepareStatement("update`countriesdata` set counter = '"+counter+"'  where country = '" + countryCode+"'");

            }else{
                //there isn't an entrance.we create it with counter=1
                counter=1;
                updateEXP = conn.prepareStatement("INSERT INTO countriesdata(country, counter) "+"VALUES ('"+countryCode+"', "+counter+")");
            }

            if(updateEXP!=null) {
                updateEXP.executeUpdate();
            }else{System.out.println("updateEXP E null");}

        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private static void sendToDatabase() {
        try {
            Iterator<Map.Entry<CharSequence,Integer>> it = sportsCount.entrySet().iterator();
            while (it.hasNext()) {
                Map.Entry<CharSequence,Integer> pair = it.next();

                String query ="SELECT * FROM data where hashtag = '"+pair.getKey().toString()+"'";
                Statement st = conn.createStatement();
                ResultSet rs = st.executeQuery(query);
                int oldcounter =0;
                if(rs.next()) {
                    oldcounter = rs.getInt("counter");
                }

                PreparedStatement updateEXP = conn.prepareStatement("update`data` set counter = '"+(pair.getValue()+oldcounter)+"'  where hashtag = '" + pair.getKey().toString() + "'");
                if(updateEXP!=null) {
                    updateEXP.executeUpdate();
                }else{System.out.println("updateEXP E null");}
            }
            counter=0;
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    public static void main(String... args) throws Exception {

        try
        {
            // Step 1: "Load" the JDBC driver
            Class.forName("com.mysql.jdbc.Driver");

            // Step 2: Establish the connection to the database
            String url = "jdbc:mysql://localhost:3306/users";
            conn = DriverManager.getConnection(url,"root","");
            if (conn == null) {
                System.out.println("Null connection");
            }
        }
        catch (Exception e)
        {
            System.err.println("D'oh! Got an exception!");
            System.err.println(e.getMessage());
        }
        runConsumer();

    }


}