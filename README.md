----- Cloud Computing PE -----
TRADEX Crypto Trading Platform

This project is made up of a PHP front and back-end using the Laravel framework along with 6 different microservices that provide basic functionality.

How to start? (run commands in root folder of each service)
PHP - Laravel:
./vendor/bin/sail build
./vendor/bin/sail up
./vendor/bin/sail artisan app:mqtt-subscriber

GO - SOAP:
docker build -t chatbot-go-soap .
docker run -d -p 4005:4005 --network crypto-exchange-network --name chatbot-go-soap-container chatbot-go-soap

Python - GRPC:
docker build -t kyc-python-grpc .
docker run -d -p 4004:4004 --network crypto-exchange-network --name kyc-python-grpc-container kyc-python-grpc 

JavaScript - GraphQL:
docker build -t login-reg-graphql .
docker run -d -p 4000:4000 --network crypto-exchange-network --name login-reg-graphql-container login-reg-graphql

JavaScript - MQTT:
docker build -t pricealerts .                                              
docker run -d -p 4002:4002 --network crypto-exchange-network --name price_alerts_js_mqtt pricealerts:latest

C# - Websockets:
docker build -t tradingserivce .                                               
docker run -d -p 4001:4001 --network crypto-exchange-network --name trading_service_cs_websockets tradingserivce:latest

Java - REST:
mvn clean package
docker build -t wallet-service .                                             
docker run -d -p 4003:4003 --network crypto-exchange-network --name wallet-java-rest wallet-service:latest

MQTT Broker:                                           
docker run -d --name mosquitto-broker -p 1883:1883 --network crypto-exchange-network -v "$(pwd)/mosquitto.conf:/mosquitto/config/mosquitto.conf" eclipse-mosquitto
