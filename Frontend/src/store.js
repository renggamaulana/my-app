import { writable, readable } from "svelte/store";

export const products = readable([
    {
        name : 'Corndog Mozarella',
        image : 'corndog.jpg',
        price : 17000,
        qty : 1
    },
    {
        name : 'Corndog Dark Choco',
        image : 'corndog_choco.jpg',
        price : 16000,
        qty : 1
    },
    {
        name : 'Hotang',
        image : 'hotang.jpg',
        price : 15000,
        qty : 1
    }
]);


export const cart = writable([])


