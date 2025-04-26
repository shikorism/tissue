import { useEffect } from 'react';

export function useScrollToTop(deps: unknown[]) {
    useEffect(() => {
        window.scrollTo(0, 0);
    }, deps);
}
