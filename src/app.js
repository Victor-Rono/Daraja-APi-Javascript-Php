async function submit() {
    // the url where the api file is located. MUST be https
    const url = 'https://fnfcom.com/api/daraja-payment.php'; // paste the code in the /php/api.php file into the url's endpoint.


    // get the phone number and amount entered by the user
    const phone = document.getElementById('phone').value;
    const amount = document.getElementById('amount').value;


    const payBill = 4021575;
    const request = 'Daraja Payment';
    const promptMessage = 'Payment Description';

    // make an axios request to the API for payment:
    const response = await axios.post(url, {
        phone,
        amount,
        payBill,
        request,
        promptMessage,
    });
    /*
    RESPONSE FROM DARAJA API
    console.log(response);
    */

}