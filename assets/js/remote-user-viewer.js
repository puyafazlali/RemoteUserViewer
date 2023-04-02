// Wait for the document to finish loading before executing any jQuery code
jQuery(document).ready(function ($) {

    // Get the URL for the AJAX handler
    const ajaxurl = remote_user_viewer_vars.ajaxurl;

    // Variable to keep track of the user ID currently being displayed
    let currentUserId = null;

    // When a remote user details link is clicked, retrieve and display the user details
    $(".remote-user-details-link").on("click", function (e) {

        // Prevent the link from navigating to a new page
        e.preventDefault();

        // Get the link element that was clicked and retrieve the user ID from the data attribute
        const $userLink = $(this);
        const userId = $userLink.data("user-id");

        // Check if the clicked user ID is the one currently being displayed and exit the function if it is
        if (userId === currentUserId) {
            return;
        }

        // Set the current user ID to the clicked user ID
        currentUserId = userId;

        // Get the user details container element and the nonce value
        let $userDetailsElement = $("#remote-user-details-container");
        let nonce = $('input[name="my_nonce"]').val();

        // Display a message while the user details are being retrieved
        $userDetailsElement.html(`<p>Loading user details...</p>`);

        // Send an AJAX request to the server to retrieve the user details
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "get_user_details",
                userId: userId,
                _wpnonce: nonce,
            },
            success: function (response) {
                // If the AJAX request is successful, build the user details table and display it
                if (response && response.success) {
                    let userDetailsElement = response.data;

                    // Create a new table element for the user details
                    let $userTable = $("<table>");

                    // Add a header row to the table
                    $userTable.append("<thead><tr><th>Field</th><th>Value</th></tr></thead>");

                    // Create a new table body element
                    let $tbody = $("<tbody>");

                    // Recursively build table rows for each property in the user details object
                    function buildTableRows(obj, prefix)
                    {
                        $.each(obj, function (key, value) {
                            let rowKey = prefix ? prefix + "." + key : key;
                            if (typeof value === "object" && value !== null) {
                                buildTableRows(value, rowKey);
                            } else {
                                $tbody.append("<tr><td>" + rowKey + "</td><td>" + value + "</td></tr>");
                            }
                        });
                    }

                    buildTableRows(userDetailsElement);

                    // Add the table body to the table element
                    $userTable.append($tbody);

                    // Replace the message with the user details table
                    $userDetailsElement.html($userTable);
                } else {
                    // If the AJAX request fails, display an error message
                    $userDetailsElement.html("<p>Error retrieving user details.</p>");
                }
            },
            error: function (xhr, status, error) {
                // If there is a server error, display an error message and log the error to the console
                $userDetailsElement.html("<p>Error retrieving user details. Please try again later.</p>");
                console.error("Error retrieving user details:", xhr, status, error);
            },
        });
    });
});
