FROM golang:1.21 AS builder

WORKDIR /app

COPY chatbot/go.mod ./

RUN go mod download

COPY chatbot/ .

RUN CGO_ENABLED=0 GOOS=linux go build -a -installsuffix cgo -o chatbot ./chatbot.go

FROM alpine:latest

RUN apk --no-cache add ca-certificates

WORKDIR /root/

COPY --from=builder /app/chatbot .

CMD ["./chatbot"]
