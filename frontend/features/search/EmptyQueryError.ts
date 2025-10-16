export class EmptyQueryError extends Error {
    constructor(message?: string) {
        super(message || 'query is empty');
        this.name = 'EmptyQueryError';
    }
}
