function serializeToObject(serializedArray) {
    const obj = {};
    serializedArray.forEach(item => {
        obj[item.name] = item.value;
    });
    return obj;
}