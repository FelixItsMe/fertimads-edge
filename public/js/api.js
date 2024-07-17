const fetchData = async (url, setting = {}) => {
    try {
        const response = await fetch(url, setting);

        // Check for successful response
        if (!response.ok) {
            if (response.status >= 400 && response.status < 500) {
                const data = await response.json()
                errorMessage(data.message);
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching data:', error);
        // You can also throw the error here to propagate it to the caller
        // throw error;

        // Handle the error here, like displaying an error message to the user
        return null; // Or return any default value to indicate error
    }
}

const errorMessage = (message) => {
    console.log(message);
    document.querySelector('#error-body').classList.remove('hidden')
    document.querySelector('#error-message').textContent = message

    setTimeout(() => {
        document.querySelector('#error-body').classList.add('hidden')
    }, 5000);
}
