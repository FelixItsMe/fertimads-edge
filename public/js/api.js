const fetchData = async (url, setting = {}) => {
    try {
        const response = await fetch(url, setting);

        // Check for successful response
        if (!response.ok) {
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
