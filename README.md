# PHP

Total Page
a.	Home – link to Index.php page
b.	My Friends – link to MyFriends.php page
c.	My Albums – link to MyAlbums.php page
d.	My Pictures – link to MyPictures.php page
e.	Upload Pictures – link to UploadPictures.php page
f.	Log In / Log Out – link to Login.php if the user is not logged in yet, or Logout.php page if the user has already logged in.

1.	Index.php 

This page is the landing (default) page of this website. 

2.	NewUser.php

This page is for a user to sign up with the system. The same validation rules as 
a)	Student ID is not blank.
b)	Name is not blank.
c)	Phone Number is in the format of nnn-nnn-nnnn
d)	Password is at least 6 characters long, contains at least one upper case, one lowercase and one digit.
passwords cannot be saved in the User table as plain text. They must be encrypted.

3.	Login.php

This page allows the signed-up users to log into the website with their student ID and password.

Except the Landing page (index.php), New User page (NewUser.php) and this page, all other pages of the website are protected, requiring users to be authenticated to access. If an unauthenticated user tries to access a protected page of the website, he/she will be redirected to this login page for authentication. Once successfully authenticated, the user will be redirected back to the protected page he/she was attempting to access.  

4.	AddAlbum

This page is used to create a new album for the user.

On this page, the user specifies the title of the album, select the accessibility from the dropdown list and optionally the detailed description of the album. 
The possible selections of dropdown list come from database table Accessibility. Currently there are two entries in the table:

•	private – The album is only accessible by the user him/her self.

•	shared – The album is accessible by the user and the user’s friend.

5.	MyAlbums.php

This page lists user’s albums. For each album, it lists the following information about the album:

•	Title – The title of the album

•	Date Updated – the last date when the user uploaded the picture(s).

•	Number of pictures – The number of pictures the album contains

•	Accessibility – the album’s accessibility. It is shown as the selection of the dropdown list. The available selections in the dropdown list come from the database table Accessibility

The user can change the of albums’ accessibility by selecting the desired accessibility from the dropdown list for each album and click the Save Change button. 

The page also has following links:

•	Create a New Album – link to AddAlbum.php page

•	Album Titles – All album titles in the album list are links to MyPictures.php page once clicked, the user is brought to MyPictures.php page with the clicked album as the selected album in the dropdown list, see MyPictures.php section for details of MyPictures.php page.

•	DELETE – a link button to delete the album. Once clicked, the system will prompt the user to confirm that all pictures in the album will be delete with the album.

6.	UploadPictures.php

The user uses this page to upload pictures into one of his/her albums.

The user can upload single or multiple pictures at a time. Optionally, the user can specify title and description for the pictures to be uploaded. When uploading multiple pictures at a time, the specified title and description apply to all the pictures uploaded.

7.	MyPictures.php

This page is for the user to browse and manage his/her pictures. The page contains the following elements:

•	A dropdown list for the user to select an album to browse its pictures.

•	A picture area showing the picture selected when the user clicks its thumbnail. 
•	A thumbnail bar displays all thumbnails of the pictures contained in the album. When a thumbnail is clicked the picture area displays the picture the thumbnail represents. The thumbnail of the picture currently in display should be highlighted with a blue border. 

•	A description and comment area showing the picture’s description (if exists) and comments (if any). The comments are ordered chronically from the newest to the oldest.

•	A text area for the user to leave a comment. The user can write a comment and click the Add Comment button to leave a comment about the picture.

When the user hovers the mouse over the picture, four icons show the actions the user can perform to the picture:
•	Rotate the picture count-clockwise
•	Rotate the picture clockwise
•	Download the original picture
•	Delete the picture from the album

8.	AddFriend.php

The user can use this page to send a friend request to another user by enter the other user’s user ID and click the Send Friend Request button.

 

A friend request has to follow the following rule:

•	The entered user ID must exists. 

•	One cannot send a friend request to himself/herself. 

•	One cannot send a friend request to someone who is already his/her friend.

•	If A sends a friend request to B, while A has a friend request from B waiting for A to accept, A and B become friends.

If the request passes the above rules, the user will get a confirmation message to confirm that the friend request has sent to the specified user:

9.	MyFriends.php

This page lists the user’s friends and friend requests. For each friend, it shows the number of shared album of that friend. The user can perform the following action for each friend.

•	Click the friend’s name to view the friend’s pictures in the shared albums

•	Select the checkbox and click Defriend Selected to remove him/her from the friend list.

For each friend request, the user can check the checkbox and click Accept Selected to accept the friend request to become a friend of the requester. Or click Deny Selected to decline the friend request. Once accepted or declined, the friend request will be removed from the friend request list.   

The page also contains a link Add Friends to page AddFriend.php

When Defriend a friend or Deny a friend request, the user should be given a proper warning:

 
10.	FriendPictures.php

This page is similar to MyPicture.php excepts:

•	The album dropdown list only has the shared albums in the list for selection.

•	No action icons will show when the user hovers the mouse over the picture. 

 











