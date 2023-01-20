


async function submit() {
    // the url where the api file is located. MUST be https
    const url = 'https://fnfcom.com/api/payments.php';

    // get the phone number and amount entered by the user
    const phoneNumber = document.getElementById('phone').value;
    const amount = document.getElementById('amount').value;

    const payBill = 4021575;
    const request = 'activation';


    // make an axios request to the API for payment:
    const response = await axios.post(url, {
        phoneNumber,
        amount,
        payBill,
        request,
    });
    console.log(response.data);
}