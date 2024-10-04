export function getCurrentDay(): string {
    const date = new Date();
    const month = date.getMonth() + 1;
    const day = date.getDate();

    return `${date.getFullYear()}-${(month > 9 ? '' : '0') + month}-${(day > 9 ? '' : '0') + day}`;
}

export function utcTimeStampToLocalTime(timestamp: number): string {
    const date = new Date(timestamp * 1000);
    const hours = date.getHours();
    const mins = date.getMinutes();
    return `${(hours > 9 ? '' : '0') + hours}:${(mins > 9 ? '' : '0') + mins}`;
}
