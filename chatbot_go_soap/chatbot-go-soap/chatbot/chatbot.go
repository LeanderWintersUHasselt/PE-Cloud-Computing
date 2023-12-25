package main

import (
	"fmt"
	"io/ioutil"
	"net/http"
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
	body, err := ioutil.ReadAll(r.Body)
	if err != nil {
		http.Error(w, "Error reading request body", http.StatusInternalServerError)
		return
	}
	defer r.Body.Close()

	fmt.Println("Received SOAP request:", string(body))

	// Process the SOAP request (e.g., parse the XML, apply business logic)
	// For simplicity, this example just echoes back the received message.

	// Create SOAP response (this should be proper XML in a real application)
	response := fmt.Sprintf("<EchoResponse>Your message was: %s</EchoResponse>", string(body))

	// Write the response
	w.Header().Set("Content-Type", "text/xml; charset=utf-8")
	w.Write([]byte(response))
}
