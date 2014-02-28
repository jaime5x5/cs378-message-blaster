<?php
session_start();
var_dump($_POST);
if(!isset($_SESSION['company_id']))
{
	header("Location: login.php");
	die();
}

require_once 'model.php';

$uname = $_SESSION['uname'];
$uid = $_SESSION['uid'];


const PAGESIZE = 10; //just hard code :)

$filter = "w"; //[w]atched, [u]nwatched, [a]ll
if( isset($_GET["filter"]) && ($_GET["filter"] == "a" || $_GET["filter"] == "u") )
		$filter = $_GET["filter"];

$pageNum = 1;
if(isset($_GET["page"]))
	$pageNum = intval($_GET["page"]);

$db = getDatabase();

$movieCount = getPageCount($uid, $filter, $db);

$pageCount = intval(ceil($movieCount / PAGESIZE));

$pageNum = min(max($pageNum, 1), $pageCount);

$results = getMovies($uid, $pageNum, PAGESIZE, $filter, $db);

$db->close();

$urle = urlencode("view.php?filter=$filter&page=$pageNum"); //I ended up using this a couple times so i will just save it here


?>
<html>
	<head>
		<title><?php echo $uname?> - View Movies</title>
	</head>
	<body>
		<h1>Welcome <?php echo $uname?> - View Movies</h1><br />
		<a href="<?php echo "addMovie.php?ref=$urle" ?>">Add Movie</a>&nbsp;&nbsp;
		<a href="<?php $_SESSION['uid'] = $uid; echo "addtmdb.php?ref=$urle" ?>">Add Movie From TMDB</a>&nbsp;&nbsp;
		<hr>
		<a href="<?php echo "view.php?filter=w" ?>">View Watched</a>&nbsp;&nbsp;
		<a href="<?php echo "view.php?filter=u" ?>">View Unwatched</a>&nbsp;&nbsp;
		<a href="<?php echo "view.php?filter=a" ?>">View All</a>&nbsp;&nbsp;
		<a href="logout.php" id="logout-button">Logout</a>
		<br /><br />
		<?php
			if($movieCount > 0):
		?>
				<table border="3">
				<tr>
					<th>Title</th>
					<th>Release Date</th>
					<th>Watched</th>
					<th>Actions</th>
				</tr>
		<?php	
				foreach ($results as $i => $a) :
		?>
				
				<tr>
					<td>
						<?php echo $a['title']; ?>
					</td>
					<td>
						<?php echo $a['rel_date']; ?>
					</td>
					<td>
						<?php echo ($a['watched'])?$a['watched_date']:"no"; ?>
					</td>
					<td>
						<a href="<?php echo "setWatched.php?mid=".$a['mid']."&ref=$urle" ?>">Mark as <?php echo ($a['watched'])?"unwatched":"watched" ?></a>&nbsp;&nbsp;
						<a href="<?php echo "movieHandler.php?mid=".$a['mid']."&ref=$urle" ?>">View / Edit</a>&nbsp;&nbsp;
						<a href="<?php echo "delete.php?mid=".$a['mid']."&ref=$urle" ?>">Delete</a>
					</td>
				</tr>
		<?php
				endforeach;
		?>
				</table>
				page <?php echo $pageNum ?> of <?php echo $pageCount ?> <br />
		<?php	
				if($pageCount > 1):
				?>
				<a href="<?php echo "view.php?filter=$filter&page=1" ?>">First</a>&nbsp;&nbsp;<a href="<?php echo "view.php?filter=$filter&page=".($pageNum-1) ?>">Prev</a>&nbsp;&nbsp;
				<?php
				for ($i=1; $i <= $pageCount ; $i++) { 
					echo "<a href=\"view.php?filter=$filter&page=$i\">$i</a>&nbsp;&nbsp;";
				}
				?>
				<a href="<?php echo "view.php?filter=$filter&page=".($pageNum+1) ?>">Next</a>&nbsp;&nbsp;<a href="<?php echo "view.php?filter=$filter&page=$pageCount" ?>">Last</a>&nbsp;&nbsp;
				<?php
				endif;
			else:
		?>
			No movies here.
		<?php endif ?>
	</body>
</html>
