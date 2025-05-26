import { QueryClient } from '@tanstack/react-query';
import { getMeQuery, getTimelinesPublicQuery } from '../api/query';

export const loader = (queryClient: QueryClient) => async () => {
    await queryClient.prefetchQuery(getMeQuery()); // ステータス欄の情報を最新にするため、常に再読み込み
    await queryClient.ensureQueryData(getTimelinesPublicQuery({ per_page: 24 }));
};
