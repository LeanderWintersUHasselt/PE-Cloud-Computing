# -*- coding: utf-8 -*-
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: kyc.proto
# Protobuf Python Version: 4.25.0
"""Generated protocol buffer code."""
from google.protobuf import descriptor as _descriptor
from google.protobuf import descriptor_pool as _descriptor_pool
from google.protobuf import symbol_database as _symbol_database
from google.protobuf.internal import builder as _builder
# @@protoc_insertion_point(imports)

_sym_db = _symbol_database.Default()




DESCRIPTOR = _descriptor_pool.Default().AddSerializedFile(b'\n\tkyc.proto\x12\x03kyc\"!\n\nKYCRequest\x12\x13\n\x0b\x64ocument_id\x18\x01 \x01(\t\"\x1f\n\x0bKYCResponse\x12\x10\n\x08verified\x18\x01 \x01(\x08\x32=\n\nKYCService\x12/\n\x08\x43heckKYC\x12\x0f.kyc.KYCRequest\x1a\x10.kyc.KYCResponse\"\x00\x62\x06proto3')

_globals = globals()
_builder.BuildMessageAndEnumDescriptors(DESCRIPTOR, _globals)
_builder.BuildTopDescriptorsAndMessages(DESCRIPTOR, 'kyc_pb2', _globals)
if _descriptor._USE_C_DESCRIPTORS == False:
  DESCRIPTOR._options = None
  _globals['_KYCREQUEST']._serialized_start=18
  _globals['_KYCREQUEST']._serialized_end=51
  _globals['_KYCRESPONSE']._serialized_start=53
  _globals['_KYCRESPONSE']._serialized_end=84
  _globals['_KYCSERVICE']._serialized_start=86
  _globals['_KYCSERVICE']._serialized_end=147
# @@protoc_insertion_point(module_scope)
