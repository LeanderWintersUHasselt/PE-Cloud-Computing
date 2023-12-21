# Specify the base image
FROM openjdk:19-jdk

# Set the working directory inside the container
WORKDIR /app

# Copy the application JAR file to the container
COPY ./target/wallet-service-1.0-SNAPSHOT.jar /app/wallet-service.jar

# Expose the port on which the application will run
EXPOSE 4003

# Set the command to run the application
CMD ["java", "-jar", "/app/wallet-service.jar"]
