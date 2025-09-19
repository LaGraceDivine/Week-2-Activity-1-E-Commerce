<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Customer Registration</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .registration-container {
      width: 400px;
      margin: 40px auto;
      padding: 25px;
      border: 1px solid #ddd;
      border-radius: 10px;
      background: #f9f9f9;
    }

    .registration-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .registration-container input,
    .registration-container select,
    .registration-container button {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      box-sizing: border-box;
    }

    #message {
      text-align: center;
      margin-top: 15px;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <div class="registration-container">
    <h2>Customer Registration</h2>

    <form id="registerForm">
      <input type="text" name="full_name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>

      <select id="country" name="country" required>
        <option value="">Select Country</option>
      </select>

      <select id="city" name="city" required>
        <option value="">Select City</option>
      </select>

      <input type="text" name="contact_number" placeholder="Contact Number" required>
      <button type="submit">Register</button>
    </form>

    <div id="message"></div>
  </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const countrySelect = document.getElementById('country');
  const citySelect = document.getElementById('city');
  const form = document.getElementById('registerForm');
  const msg = document.getElementById('message');

  /** 
   * Loading all countries from REST Countries API alphabetically
   */
  fetch('https://restcountries.com/v3.1/all?fields=name,cca2')
    .then(res => res.json())
    .then(data => {
      const sortedCountries = data
        .filter(c => c.cca2 && c.name && c.name.common)
        .sort((a, b) => a.name.common.localeCompare(b.name.common));

      //Adding each country to the dropdown
      sortedCountries.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.cca2; 
        opt.textContent = c.name.common;
        countrySelect.appendChild(opt);
      });
    })
    .catch(err => {
      console.error("Error loading countries:", err);
      countrySelect.innerHTML = '<option value="">Error loading countries</option>';
    });

  /**
   * Loading cities when a country is selected
   */
  countrySelect.addEventListener('change', () => {
    const selectedCountryCode = countrySelect.value;
    citySelect.innerHTML = '<option>Loading...</option>';

    if (!selectedCountryCode) {
      citySelect.innerHTML = '<option value="">Select City</option>';
      return;
    }

    fetch(`actions/get_cities_action.php?country=${encodeURIComponent(selectedCountryCode)}`)
      .then(res => res.json())
      .then(data => {
        citySelect.innerHTML = '';
        if (Array.isArray(data.cities) && data.cities.length > 0) {
          data.cities.sort((a, b) => a.localeCompare(b));
          data.cities.forEach(city => {
            const opt = document.createElement('option');
            opt.value = city;
            opt.textContent = city;
            citySelect.appendChild(opt);
          });
        } else {
          citySelect.innerHTML = '<option>No cities found</option>';
        }
      })
      .catch(err => {
        console.error("Error loading cities:", err);
        citySelect.innerHTML = '<option>Error loading cities</option>';
      });
  });

  /**
   * Form submit handler
   */
  form.addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(form);

    fetch('actions/register_customer_action.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        msg.textContent = data.message || 'No response message';
        msg.style.color = data.status === 'success' ? 'green' : 'red';
        if (data.status === 'success') {
          setTimeout(() => window.location.href = 'login.php', 1500);
        }
      })
      .catch(err => {
        console.error("Error submitting form:", err);
        msg.textContent = 'An error occurred while registering.';
        msg.style.color = 'red';
      });
  });
});
</script>

</body>
</html>