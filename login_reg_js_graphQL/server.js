const express = require('express');
const { ApolloServer, gql } = require('apollo-server-express');
const cors = require('cors');
const typeDefs = require('./typeDefs')
const resolvers = require('./resolvers');

const app = express();
app.use(cors());


const server = new ApolloServer({
    typeDefs,
    resolvers,
    introspection: true, // Enables introspection of the schema
    playground: true    // Enables the GraphQL Playground
  });

  
async function startServer() {
  await server.start();
  server.applyMiddleware({ app });

  const PORT = process.env.PORT || 4000;
  app.listen(PORT, () =>
    console.log(`Server ready at http://localhost:${PORT}${server.graphqlPath}`)
  );
}

startServer();