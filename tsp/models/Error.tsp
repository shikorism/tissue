namespace Tissue;

@error
model Error {
  status: int32;
  error: {
    message: string;
  };
}

@error
model ValidationError {
  status: int32;
  error: {
    message: string;
    violations: {
      message: string;
      field: string;
    }[];
  };
}
