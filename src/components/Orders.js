import { useState, useEffect } from "react";
import axios from "axios";

export default function Order(){
    const [posts, setPosts] = useState([]);


    useEffect(() => {
        getPosts();
    }, []);

    function getPosts() {
        axios.get(`https://duckcave.com/api/endpoints/orders`).then(function(response) {
            setPosts(response.data);

        });
    }
    return(
        <div class="flex flex-col bg-gray-100">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                <table class="min-w-full text-left text-sm font-light">
                    <thead class="border-b font-medium dark:border-neutral-500">
                    <tr>
                        <th scope="col" class="px-6 py-4">PayID</th>
                        <th scope="col" class="px-6 py-4">Wdate</th>
                        <th scope="col" class="px-6 py-4">Amount</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    {posts.map((post, key) =>
                    <tr class="border-b dark:border-neutral-500">
                        <td class="whitespace-nowrap px-6 py-4 font-medium">{post.payid}</td>
                        <td class="whitespace-nowrap px-6 py-4">{post.webhook_date}</td>
                        <td class="whitespace-nowrap px-6 py-4">{post.amount}</td>
                        <td class="whitespace-nowrap px-6 py-4">{post.orderstatus}</td>
                    </tr>
                    )}
                    </tbody>
                </table>
                </div>
            </div>
            </div>
        </div> 
    );
}