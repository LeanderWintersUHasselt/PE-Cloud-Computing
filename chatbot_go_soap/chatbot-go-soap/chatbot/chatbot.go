package main

import (
	"encoding/xml"
	"fmt"
	"io"
	"net/http"
	"strings"
)

func main() {
	http.HandleFunc("/soap", soapHandler)
	http.ListenAndServe(":4005", nil)
}

func soapHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != "POST" {
		http.Error(w, "Invalid request method", http.StatusMethodNotAllowed)
		return
	}

	// Read the request body
	body, err := io.ReadAll(r.Body)
	if err != nil {
		http.Error(w, "Error reading request body", http.StatusInternalServerError)
		return
	}
	defer r.Body.Close()

	fmt.Println("Received SOAP request:", string(body))

	// Extract the actual message from the SOAP request
	message, err := extractMessageFromBody(string(body))
	if err != nil {
		http.Error(w, "Error processing request", http.StatusInternalServerError)
		return
	}

	// Generate a response based on the extracted message
	responseMessage := processMessage(message)

	// Create and send a SOAP response
	response := createSOAPResponse(responseMessage)
	fmt.Println(response)
	w.Header().Set("Content-Type", "text/xml; charset=utf-8")
	w.Write([]byte(response))
}

// Dummy function to extract message from SOAP body
func extractMessageFromBody(body string) (string, error) {
	type SOAPBody struct {
		XMLName xml.Name `xml:"Body"`
		Message string   `xml:"message"`
	}

	type SOAPEnvelope struct {
		XMLName xml.Name `xml:"Envelope"`
		Body    SOAPBody
	}

	var envelope SOAPEnvelope
	err := xml.Unmarshal([]byte(body), &envelope)
	if err != nil {
		return "", err
	}

	return envelope.Body.Message, nil
}

// processMessage processes the incoming message and generates a response
func processMessage(message string) string {
	message = strings.ToLower(message) // Convert message to lowercase for case-insensitive comparison

	switch {
	case strings.Contains(message, "hello") || strings.Contains(message, "hi") || strings.Contains(message, "hey"):
		return "Hello! How can I help you?"
	case strings.Contains(message, "home"):
		return "Do you want to go home? Click 'Home' in the navigation bar above."

	case strings.Contains(message, "trade"):
		return "It looks like you want to know about Trading. Click 'Trade' in the navigation bar above."

	case strings.Contains(message, "wallet") || strings.Contains(message, "balance") || strings.Contains(message, "deposit") || strings.Contains(message, "withdraw"):
		return "You are asking about your Wallet. Check your balance, make deposits and withdrawals by clicking 'Wallet' in the navigation bar above."

	case strings.Contains(message, "price") || strings.Contains(message, "alert"):
		return "If you're interested in Price Alerts. Navigate to 'Price Alerts' in the navigation bar above."

	default:
		return fmt.Sprintf("I'm not sure how to respond to '%s'. Can you please ask something else?", message)
	}
}

// Function to create a SOAP response
func createSOAPResponse(message string) string {
	return fmt.Sprintf("<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/'>"+
		"<soapenv:Body>"+
		"<messageResponse>%s</messageResponse>"+
		"</soapenv:Body>"+
		"</soapenv:Envelope>", message)
}
