interface LogRequest {
  path: string;
  method: string;
}

interface LogResponse {
  status: number;
  duration: number;
}

export interface LogBody {
  request: LogRequest;
  response: LogResponse;
  id: string;
}
