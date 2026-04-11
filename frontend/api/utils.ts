export const ensure = <T>(value: T | undefined | null): T => {
    if (value === undefined || value === null) {
        throw new Error('Value is undefined or null');
    }
    return value;
};

export const totalCount = (response: Response): number | undefined => {
    const total = response.headers.get('X-Total-Count');
    return total ? parseInt(total, 10) : undefined;
};
