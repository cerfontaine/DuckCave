export default function Footer(){
    return(
        <section class="sm:mb-12 mb-24">
            <div
                class="alert alert-dismissible fade show fixed bottom-0 right-0 left-0 z-[1030] w-full items-center justify-between bg-neutral-900 py-4 px-6 text-center text-white md:flex md:text-left">
                <div class="mb-4 flex flex-wrap items-center justify-center md:mb-0 md:justify-start">
                <span class="mr-2 [&>svg]:h-5 [&>svg]:w-5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="text-white">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" />
                    </svg>
                </span>
                <strong class="mr-1">This website is for testing purpose !!</strong> Do not share real data on this website.
                </div>
            </div>
        </section>
    );
}