export function formatOrDefault<T>(
    input: T | null | undefined,
    formatter: (_: T) => string,
    defaultValue = '\u{2015}',
): string {
    return input ? formatter(input) : defaultValue;
}

/**
 * 数値をカンマ区切りでフォーマットします。
 */
export function formatNumber(num: number): string {
    return num.toLocaleString('ja-JP');
}

/**
 * 通算秒数を日数と時分にフォーマットします。
 * @param seconds 通算秒数
 * @return "xx日 xx時間 xx分" 形式でフォーマットされた文字列
 */
export function formatInterval(seconds: number): string {
    const days = formatNumber(Math.floor(seconds / 86400));
    const hours = Math.floor((seconds % 86400) / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);

    return `${days}日 ${hours}時間 ${minutes}分`;
}
