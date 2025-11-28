const ticketPrice = document.getElementById('ticket_price');
selectValueMapping = {
    user_type: (item) => item?.user_type,
};
inputValueFormatter = {};

document.addEventListener("DOMContentLoaded", () => {
    applyIntegerValidation([ticketPrice.id]);

    ticketPrice.addEventListener('input', (e)=>{
        updateFormattedSpan(ticketPrice)
    })
});
