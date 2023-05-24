function multiplyWithoutOperator(a, b) {

    if (b === 0 || a === 0) {
        return 0;
    }

    if (b < 0) {
        return -multiplyWithoutOperator(a, -b);
    }
 
    return a + multiplyWithoutOperator(a, b - 1);
}

const product = multiplyWithoutOperator(-2, 4);
console.log(product); // Output: 20