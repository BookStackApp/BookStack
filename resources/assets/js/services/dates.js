
export function getCurrentDay() {
    let date = new Date();
    let month = date.getMonth() + 1;
    let day = date.getDate();

    return `${date.getFullYear()}-${(month>9?'':'0') + month}-${(day>9?'':'0') + day}`;
}

export function utcTimeStampToLocalTime(timestamp) {
    let date = new Date(timestamp * 1000);
    let hours = date.getHours();
    let mins = date.getMinutes();
    return `${(hours>9?'':'0') + hours}:${(mins>9?'':'0') + mins}`;
}