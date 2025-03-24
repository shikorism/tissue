import React from 'react';
import { GlobalNavigation } from '../components/GlobalNavigation';

export const Home: React.FC = () => (
    <>
        <GlobalNavigation />
        <div className="ml-(--global-nav-width) p-4">
            <div className="p-3 max-w-[1000px] flex bg-gray-back rounded">
                <div className="flex-1">
                    <h1 className="text-lg font-bold">現在のセッション</h1>
                    <p className="my-2 text-xl">0日 0時間 0分</p>
                    <p className="text-sm">2000/01/01 00:00 にリセット</p>
                </div>
                <table className="flex-1 text-sm">
                    <tbody>
                        <tr>
                            <th className="pr-2 text-right after:content-[':']">通算回数</th>
                            <td>0回</td>
                        </tr>
                        <tr>
                            <th className="pr-2 text-right after:content-[':']">平均記録</th>
                            <td>0日 0時間 0分</td>
                        </tr>
                        <tr>
                            <th className="pr-2 text-right after:content-[':']">中央値</th>
                            <td>0日 0時間 0分</td>
                        </tr>
                    </tbody>
                </table>
                <table className="flex-1 text-sm">
                    <tbody>
                        <tr>
                            <th className="pr-2 text-right after:content-[':']">最長記録</th>
                            <td>0日 0時間 0分</td>
                        </tr>
                        <tr>
                            <th className="pr-2 text-right after:content-[':']">最短記録</th>
                            <td>0日 0時間 0分</td>
                        </tr>
                        <tr>
                            <th className="pr-2 text-right after:content-[':']">合計時間</th>
                            <td>0日 0時間 0分</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div className="mt-4">
                <h1 className="text-xl">お惣菜コーナー</h1>
                <p className="mt-2 text-sm text-secondary">
                    最近の公開チェックインから、オカズリンク付きのものを表示しています。
                </p>
            </div>
        </div>
    </>
);
