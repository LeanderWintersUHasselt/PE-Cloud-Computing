package com.cloud.walletservice.controller;

import java.io.IOException;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/wallet")
public class WalletController {

    private final WalletService walletService;

    @Autowired
    public WalletController(WalletService walletService) {
        this.walletService = walletService;
    }

    @GetMapping("/balance")
    public ResponseEntity<?> getBalance(@RequestParam int userId) {
        try {
            BalanceData balanceData = walletService.getBalancesForUser(userId);
            return ResponseEntity.ok(balanceData);
        } catch (Exception e) {
            return ResponseEntity.status(500).body("Error fetching balances");
        }
    }

    @PostMapping("/deposit")
    public ResponseEntity<?> deposit(@RequestBody WalletRequest request) {
        try {
            String responseMessage = walletService.deposit(request.getUserId(), request.getAmount());
            return ResponseEntity.ok(responseMessage);
        } catch (IOException e) {
            return ResponseEntity.status(500).body("Error processing deposit");
        }
    }
    
    @PostMapping("/withdraw")
    public ResponseEntity<?> withdraw(@RequestBody WalletRequest request) {
        try {
            String responseMessage = walletService.withdraw(request.getUserId(), request.getAmount());
            return ResponseEntity.ok(responseMessage);
        } catch (IOException e) {
            return ResponseEntity.status(500).body("Error processing withdrawal");
        }
    }    
}