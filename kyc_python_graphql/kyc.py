from concurrent import futures
import grpc
import kyc_pb2_grpc
import kyc_pb2

class KYCService(kyc_pb2_grpc.KYCServiceServicer):

    def CheckDocument(self, request, context):
        content = request.content.decode('utf-8')
        if "approved" in content:
            return kyc_pb2.VerificationResponse(verified=True)
        else:
            return kyc_pb2.VerificationResponse(veriefied=False)


def serve():
    server = grpc.server(futures.ThreadPoolExecutor(max_workers=10))
    kyc_pb2_grpc.add_KYCServiceServicer_to_server(KYCService(), server)
    server.add_insecure_port('[::]:4004')
    server.start()
    server.wait_for_termination()

if __name__ == '__main__':
    serve()
