<?php 
include 'Database.php';
include 'functions.php';

// Connect to DB and fetch all members
$db = new Database('localhost', 'abdulrahman', 'root', '');
$members = $db->fetchMembers(); // All members
$tree = buildTree($members);    // Nested array for tree view

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Members Tree</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Fancybox v3 (used for modal) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
</head>
<body>

    <!-- Member Tree List -->
    <ul id="members-list">
        <?php
        // Recursive function to render nested <ul><li> tree
        function renderTree($tree) {
            foreach ($tree as $node) {
                echo "<li data-id='{$node['Id']}'>{$node['Name']}
                 <button class='edit-member'>Edit</button> &nbsp; &nbsp
                <button class='delete-member'>Delete</button>";
                if (isset($node['children'])) {
                    echo "<ul style='margin-top:10px'>";
                    renderTree($node['children']);
                    echo "</ul>";
                }
                echo "</li>";
            }
        }
        renderTree($tree);
        ?>
    </ul>

    <!-- Add Member Button -->
    <button id="add-member-btn">Add Member</button>

    <!-- Fancybox Modal Form -->
    <div id="add-member-modal" style="display:none;">
        <form id="add-member-form">
            <label for="parent">Parent:</label>
            <select id="parent" name="parent" style="width: 75%;">
                <option value="">Select</option>
                <?php
                // Dynamically populate dropdown from DB members
                foreach ($members as $member) {
                    echo "<option value='{$member['Id']}'>{$member['Name']}</option>";
                }
                ?>
            </select><br><br>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br><br>

            <button type="submit">Save</button>
        </form>
    </div>

    <!-- Edit Member Modal -->
<div id="edit-member-modal" style="display:none;">
    <form id="edit-member-form">
        <input type="hidden" id="edit-id" name="id">
        <label for="edit-name">New Name:</label>
        <input type="text" id="edit-name" name="name" required>
        <button type="submit">Update</button>
    </form>
</div>

    <!-- Custom JS for modal, ajax, and tree update -->
    <script>
        $(document).ready(function () {
    // Open modal using Fancybox
    $('#add-member-btn').click(function () {
        $.fancybox.open({
            src: '#add-member-modal',
            type: 'inline'
        });
    });

    // Handle form submission
    $('#add-member-form').submit(function (e) {
        e.preventDefault();
        var name = $('#name').val().trim();
        var parent = $('#parent').val();

        if (!/^[a-zA-Z\s]+$/.test(name)) {
            alert('Name must contain only letters and spaces.');
            return;
        }

        $.ajax({
            url: 'add_member.php',
            method: 'POST',
            data: { name: name, parentId: parent },
            success: function (response) {
                try {
                    var newMember = JSON.parse(response);
                    var newLi = `<li data-id='${newMember.Id}'>${newMember.Name}</li>`;

                    if (parent) {
                        var parentLi = $(`li[data-id='${parent}']`);
                        // Append to existing child UL or create new UL
                        if (parentLi.children('ul').length) {
                            parentLi.children('ul').append(newLi);
                        } else {
                            parentLi.append(`<ul>${newLi}</ul>`);
                        }
                    } else {
                        $('#members-list').append(newLi);
                    }

                    // Add new member to Parent dropdown
                    $('#parent').append(
                        $('<option></option>').val(newMember.Id).text(newMember.Name)
                    );

                    $.fancybox.close();
                    $('#add-member-form')[0].reset(); // Reset form
                } catch (err) {
                    alert('Error parsing response. Please check server.');
                }
            },
            error: function () {
                alert('Error submitting data.');
            }
        });
    });

    // EDIT
    $('.edit-member').on('click', function () {
        var li = $(this).closest('li');
        var id = li.data('id');
        var name = li.contents().get(0).nodeValue.trim(); // Get just the text (not buttons)

        $('#edit-id').val(id);
        $('#edit-name').val(name);
        $.fancybox.open({ src: '#edit-member-modal', type: 'inline' });
    });

    // Submit Edit Form using PUT method via AJAX
    $('#edit-member-form').submit(function (e) {
        e.preventDefault();
        var id = $('#edit-id').val();
        var name = $('#edit-name').val().trim();

        if (!/^[a-zA-Z\s]+$/.test(name)) {
            alert('Name must contain only letters and spaces.');
            return;
        }

        $.ajax({
            url: 'update_member.php',
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({ id: id, name: name }),
            success: function (response) {
                var updated = JSON.parse(response);
                $("li[data-id='" + id + "']").contents().first()[0].nodeValue = updated.Name + ' ';
                $.fancybox.close();
            },
            error: function () {
                alert('Failed to update member.');
            }
        });
    });

    // DELETE Member using AJAX
            $('.delete-member').on('click', function () {
                var li = $(this).closest('li');
                var id = li.data('id');

                if (confirm("Are you sure you want to delete this member?")) {
                    $.ajax({
                        url: 'delete_member.php',
                        method: 'DELETE',
                        contentType: 'application/json',
                        data: JSON.stringify({ id: id }),
                        success: function (response) {
                            // On successful deletion, remove the member from the tree
                            li.remove();
                        },
                        error: function () {
                            alert('Failed to delete member.');
                        }
                    });
                }
            });
});

    </script>

</body>
</html>
