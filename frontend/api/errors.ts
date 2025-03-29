export class ResponseError extends Error {
    response: Response;

    constructor(response: Response, ...rest: any) {
        super(...rest);
        this.name = 'ResponseError';
        this.response = response;
    }

    static castFrom(error: unknown): ResponseError | null {
        return error instanceof ResponseError ? error : null;
    }
}

export class UnauthorizedError extends ResponseError {
    constructor(response: Response) {
        super(response, `${response.status} ${response.statusText}`);
        this.name = 'UnauthorizedError';
    }

    static castFrom(error: unknown): UnauthorizedError | null {
        return error instanceof UnauthorizedError ? error : null;
    }
}
