import { SortKey } from './SortKeySelect';
import { compareAsc, parseISO } from 'date-fns';

export function sortAndFilteredCollections(
    collections: Tissue.Collection[],
    sort: SortKey,
    filter: string,
): Tissue.Collection[] {
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
