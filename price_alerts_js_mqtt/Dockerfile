# Use an official Node runtime as the base image
FROM node:14

# Set the working directory in the container
WORKDIR /usr/src/app

# Copy package.json and package-lock.json
COPY package*.json ./

# Install project dependencies
RUN npm install

# Bundle app source
COPY . .

# Your app binds to port 8080 so use the EXPOSE instruction to have it mapped
EXPOSE 4002

# Define the Docker image's command to run your app
CMD [ "node", "price_alerts.js" ]
