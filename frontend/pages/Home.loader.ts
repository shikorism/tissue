import { QueryClient } from '@tanstack/react-query';
import { getInformationLatestQuery, getMeQuery } from '../api/query';

export const loader = (queryClient: QueryClient) => async () => {
    await Promise.all([
        queryClient.prefetchQuery(getMeQuery()), // ステータス欄の情報を最新にするため、常に再読み込み
        queryClient.ensureQueryData(getInformationLatestQuery()),
    ]);
};
