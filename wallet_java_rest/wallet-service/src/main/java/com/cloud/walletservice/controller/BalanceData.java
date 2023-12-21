package com.cloud.walletservice.controller;

public class BalanceData {
    private double EUR;
    private double BTC;
    private double ETH;

    // Getters and Setters
    public double getEUR() {
        return EUR;
    }
    public void setEUR(double EUR) {
        this.EUR = EUR;
    }

    public double getBTC() {
        return BTC;
    }
    public void setBTC(double BTC) {
        this.BTC = BTC;
    }

    public double getETH() {
        return ETH;
    }
    public void setETH(double ETH) {
        this.ETH = ETH;
    }
}
