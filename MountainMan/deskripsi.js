const apiKey = 'ba857300bdc774a44b98bf9a7ad3c97a';

window.onload = () => {
    const city = 'Probolinggo';
    getWeather(city);
};

async function getWeather(city) {
    try {
        const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`);
        if (!response.ok) {
            throw new Error('Location not found');
        }
        const data = await response.json();
        displayWeather(data);
    } catch (error) {
        document.getElementById('weatherResult').innerText = error.message;
    }
}

function displayWeather(data) {
    const weatherResult = document.getElementById('weatherResult');
    const temperature = data.main.temp;
    const weatherDescription = data.weather[0].description;
    const city = data.name;
    const humidity = data.main.humidity;
    const windSpeed = data.wind.speed;
    
    // Ambil icon cuaca dari OpenWeatherMap
    const iconCode = data.weather[0].icon;
    const iconUrl = `https://openweathermap.org/img/wn/${iconCode}.png`;

    // Tampilkan hasil dengan ikon cuaca
    weatherResult.innerHTML = `
        <div class="weather-info">
            <h2>Weather in ${city}</h2>
            <img src="${iconUrl}" alt="Weather Icon" class="weather-icon">
            <p class="temperature">Temperature: <strong>${temperature} °C</strong></p>
            <p class="description_con">Condition: <strong>${weatherDescription}</strong></p>
            <p>Humidity: ${humidity}%</p>
            <p>Wind Speed: ${windSpeed} m/s</p>
        </div>
    `;
}
