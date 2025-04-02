export class ResponseError extends Error {
    response: Response;
    error: any;

    constructor(response: Response, body: string) {
        let message = `${response.status} ${response.statusText}`;
        let error;
        try {
            error = JSON.parse(body);
            if (error.message) {
                message = error.message;
            }
        } catch {
            error = body;
        }
        super(message);

        this.name = 'ResponseError';
        this.response = response;
        this.error = error;
    }
}
