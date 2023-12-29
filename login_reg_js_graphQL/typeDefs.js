const { gql } = require('apollo-server-express');

const typeDefs = gql`
  type Query {
    getCustomerById(id: Int!): Customer
    getAlertsByIds(ids: [String]!): [Alert]
    getUserBalances(userId: Int!): UserBalances
  }

  enum CoinType {
    BTC
    ETH
  }

  type Mutation {
    login(email: String!, password: String!): LoginResponse
    register(email: String!, password: String!, firstName: String!, lastName: String!): RegisterResponse
    createAlert(alertId: String!, message: String!): AlertResponse
    deleteAlertsByIds(ids: [String]!): DeleteResponse
    updateEuroBalance(userId: Int!, amount: Float!): BalanceUpdateResponse
    updateCoinBalance(userId: Int!, amount: Float!, coin: CoinType!): BalanceUpdateResponse
  }

  type Alert {
    alert_id: String
    message: String
  }

  type Customer {
    firstName: String
    lastName: String
    email: String
    userId: Int
  }

  type UserBalances {
    eur_balance: Float
    btc_balance: Float
    eth_balance: Float
  }

  type LoginResponse {
    success: Boolean!
    message: String
    token: String
    userId: Int
  }

  type RegisterResponse {
    success: Boolean!
    message: String
    user: Customer
  }

  type AlertResponse {
      success: Boolean!
      message: String
  }

  type DeleteResponse {
    success: Boolean!
    message: String
  }

  type BalanceUpdateResponse {
      success: Boolean!
      message: String
  }
`;

module.exports = typeDefs;
