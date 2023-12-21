import grpc
import kyc_pb2_grpc
import kyc_pb2

def run():
    with grpc.insecure_channel('localhost:4004') as channel:
        stub = kyc_pb2_grpc.KYCServiceStub(channel)
        response = stub.CheckKYC(kyc_pb2.KYCRequest(user_id="123", document_id="456"))
        print("KYC server response: " + response.message)

if __name__ == '__main__':
    run()
