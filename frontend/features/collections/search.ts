import { compareAsc, parseISO } from 'date-fns';
import type { components } from '../../api/schema';

type Collection = components['schemas']['Collection'];
export type SortKey = 'id:asc' | 'id:desc' | 'name:asc' | 'name:desc' | 'updated_at:asc' | 'updated_at:desc';

export function sortAndFilteredCollections(collections: Collection[], sort: SortKey, filter: string): Collection[] {
    const sortedCollections = [...collections].sort((a, b) => {
        const [field] = sort.split(':');
        switch (field) {
            case 'id':
                return a.id - b.id;
            case 'name':
                return a.title.localeCompare(b.title);
            case 'updated_at':
                return compareAsc(parseISO(a.updated_at), parseISO(b.updated_at));
            default:
                throw 'invalid sort key';
        }
    });
    if (sort.split(':')[1] === 'desc') {
        sortedCollections.reverse();
    }

    return sortedCollections.filter((collection) => {
        return collection.title.toLowerCase().includes(filter.toLowerCase());
    });
}
