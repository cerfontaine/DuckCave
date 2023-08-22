import { useState } from "react";
import axios from "axios";

export default function Cart(){
    const [inputs, setInputs] = useState({amount: 1});

    const handleChange = (event) => {
        const name = event.target.name;
        const value = event.target.value;
        setInputs(values => ({...values, [name]: value}));
    }

    const handleSubmit = (event) => {
        event.preventDefault();
        console.log(inputs.amount);

        axios.post('https://duckcave.com/api/endpoints/hostedcheckout', inputs).then(function (response) {
            console.log(response.data);
            window.location.replace(response.data);

            
        });
    }

    return(
        <div class="sm:h-screen bg-gray-100 pt-20 h-fit pb-20">
            <form onSubmit={handleSubmit}>
            <h1 class="mb-10 text-center text-2xl font-bold">Cart Items</h1>
            <div class="mx-auto max-w-5xl justify-center px-6 md:flex md:space-x-6 xl:px-0">
            <div class="rounded-lg md:w-2/3">
                <div class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:flex sm:justify-start">
                <img src="https://duckcave.com/assets/mug.png" alt="product-image" class="w-full rounded-lg sm:w-40" />
                <div class="sm:ml-4 sm:flex sm:w-full sm:justify-between">
                    <div class="mt-5 sm:mt-0">
                    <h2 class="text-lg font-bold text-gray-900">DuckCave mug</h2>
                    <p class="mt-1 text-xs text-gray-700">This is a great mug and has only one size</p>
                    </div>
                    <div class="mt-4 flex justify-between sm:space-y-6 sm:mt-0 sm:block sm:space-x-6">
                    <div class="flex items-center border-gray-100">
                        <input class="h-8 w-8 border bg-white text-center text-xs outline-none" min="0" value={inputs.amount} name="amount" type="number" onChange={handleChange} />
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="mt-6 h-full rounded-lg border bg-white p-6 shadow-md md:mt-0 md:w-1/3">
                <div class="mb-2 flex justify-between">
                <p class="text-gray-700">Subtotal</p>
                <p class="text-gray-700">{inputs.amount} €</p>
                </div>
                <div class="flex justify-between">
                <p class="text-gray-700">Shipping</p>
                <p class="text-gray-700">FREE</p>
                </div>
                <hr class="my-4" />
                <div class="flex justify-between">
                <p class="text-lg font-bold">Total</p>
                <div class="">
                    <p class="mb-1 text-lg font-bold">{inputs.amount} EUR</p>
                    <p class="text-sm text-gray-700">including VAT</p>
                </div>
                </div>
                <button class="mt-6 w-full rounded-md bg-blue-500 py-1.5 font-medium text-blue-50 hover:bg-blue-600" >Check out</button>
            </div>
            </div>
            </form>
         </div>
    );
}