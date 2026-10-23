const typeInput = document.querySelector("#type");
const brandInput = document.querySelector("#brand");
async function checkProduct() {
  const type = typeInput.value.trim();
  const brand = brandInput.value.trim();
  const typeMessage = document.querySelector("#type-message");
  const brandMessage = document.querySelector("#brand-message");
  typeMessage.textContent = type ? "Checking type..." : "No type sent yet!";
  brandMessage.textContent = brand ? "Checking brand..." : "No brand sent yet!";
  try {
    const query = new URLSearchParams( {
      type, brand
    }
    );
    const response = await fetch("task04.php?" + query);
    const data = await response.json();
    typeMessage.textContent = data.typeMessage;
    typeMessage.className = data.typeValid ? "text-success" : "text-danger";
    brandMessage.textContent = data.brandMessage;
    brandMessage.className = data.brandValid ? "text-success" : "text-danger";
  }
  catch {
    typeMessage.textContent = "Could not check the type.";
    brandMessage.textContent = "Could not check the brand.";
    typeMessage.className = "text-danger";
    brandMessage.className = "text-danger";
  }
}
typeInput.addEventListener("input", checkProduct);
brandInput.addEventListener("input", checkProduct);
