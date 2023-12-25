from concurrent import futures
import grpc
import kyc_pb2_grpc
import kyc_pb2
import logging

class KYCService(kyc_pb2_grpc.KYCServiceServicer):

    def CheckKYC(self, request, context):
        content = request.document_content.lower()
        logging.info("Received document content: %s", content)
        if "approved" in content:
            return kyc_pb2.KYCResponse(verified=True)
        else:
            return kyc_pb2.KYCResponse(verified=False)


def serve():
    server = grpc.server(futures.ThreadPoolExecutor(max_workers=10))
    kyc_pb2_grpc.add_KYCServiceServicer_to_server(KYCService(), server)
    server.add_insecure_port('[::]:4004')
    server.start()
    server.wait_for_termination()

if __name__ == '__main__':
    serve()
