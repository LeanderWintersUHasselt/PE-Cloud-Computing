FROM openjdk:19-jdk

WORKDIR /app

COPY ./target/wallet-service-1.0-SNAPSHOT.jar /app/wallet-service.jar

EXPOSE 4003

CMD ["java", "-jar", "/app/wallet-service.jar"]
