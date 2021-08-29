# ResumeRegistry

## Necessary Software
I use MAMP to run my SQL server, MAMP is available at https://www.mamp.info/en/downloads/. You will also need to create the tables in the SQL server.

## Demo
A demo can be seen at https://youtu.be/ENi-VVnlCSk. 

Note that there is no audio, it is a quick video to show the application and database uploading.

## Description
This is a resume registry developed using PHP, MySQL, jQuery, JavaScript, HTML, and CSS. It was developed to have CRUD functionality and follows a Model-View-Controller design.

You can sign up and sign in to the web application and then create, read, update, and delete profiles. Each profile has a first name, last name, email, headline, and summary as mandatory sections. Additionally, you can add education and positions.

For the eduction I have autocompletion that uses the institution database shown in my demo to autocomplete previously entered schools. Whenever someone enters a school that is not recognized it is added to the institution database.

I used htmlentities() and PDO prepare statements to avoid SQL and HTML injection.

Features Implemented

<ul>
  <li>Sign In Page: You can create an account, note that once you sign in you can only see profiles that you own</li>
  <li>Sign Up Page: You can create an account note that passwords are stored with a salted hash for security</li>
  <li>Create: You can create profiles through the "Add New Entry" feature (only if you are signed in)</li>
  <li>Read: A table is shown for viewing the data, click on a specific profile name to see more info</li>
  <li>Update: Click the edit link to update an entry (only if you are signed in and you own the profile)</li>
  <li>Delete: Click the delete link to delete an entry (only if you are signed in and you own the profile)</li>
</ul>

## Pictures
![image](https://user-images.githubusercontent.com/53048085/131262993-10b8d19b-6a60-467f-9d83-6fcb295071bd.png)
![image](https://user-images.githubusercontent.com/53048085/131262999-5cd1f4b9-4b5f-40bc-9619-798b3aaec42a.png)
![image](https://user-images.githubusercontent.com/53048085/131263007-175d5f83-1413-4477-9143-93c4f6f6b2b8.png)
![image](https://user-images.githubusercontent.com/53048085/131263021-0cb577f7-db34-4c46-ba29-24da4a7efc84.png)
![image](https://user-images.githubusercontent.com/53048085/131263028-b08da1e5-be95-44c0-bdcf-6023e6392f16.png)
![image](https://user-images.githubusercontent.com/53048085/131263050-dc3dd1e6-2b76-4ba5-94fa-19937c8a3427.png)
![image](https://user-images.githubusercontent.com/53048085/131263055-cc9d56ac-4d64-4cb0-818f-0a8024301a22.png)
![image](https://user-images.githubusercontent.com/53048085/131263066-08b96c3b-9e21-4b15-bb52-38eb0752a200.png)

