console.log("JS connected");
function validateSearch() {
    let source = document.querySelector("input[name='source']").value;
    let destination = document.querySelector("input[name='destination']").value;
    let date = document.querySelector("input[name='date']").value;

    if (source === "" || destination === "" || date === "") {
        alert("Please fill all fields");
        return false;
    }

    alert("Searching flights...");
    return true;
}