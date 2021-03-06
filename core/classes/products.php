<?php

class products {

    private $db;

    public $id;
    public $name;
    public $description;
    public $catgory;
    public $collection;
    public $gender;

    public function __construct() {
        global $db;
        $this->db = $db;
    }
    public function getProduct($id) {
        $params = array(
            $id
        );
        $sql = "SELECT product.*, gender.gender, collection.name AS collection_name FROM product 
				INNER JOIN gender 
				ON product.gender = gender.id 
				INNER JOIN collection 
				ON product.collection_id = collection.id
			  	WHERE product.id = ?";
	    $row = $this->db->fetch_array($sql, $params);

	    if (count($row)){
		    $row = call_user_func_array('array_merge', $row);

		    $this->id = $row['id'];
		    $this->name = $row['name'];
		    $this->thumbnail = $row['thumbnail'];
		    $this->description = $row['description'];
		    $this->gender = $row['gender'];
		    $this->collection_name = $row['collection_name'];
	    }
    }

    public function getProducts() {
        $sql = "SELECT * FROM product";
        return $this->db->fetch_array($sql);
    }
    public function searchProducts($query) {
        $sql = "SELECT product.*, gender.gender, collection.name AS collection_name, category.name AS category_name FROM product
              INNER JOIN gender
              ON product.gender = gender.id
              INNER JOIN collection
              ON product.collection_id = collection.id
              INNER JOIN category
              ON product.category_id = category.id
              WHERE product.name LIKE '%$query%'
              OR collection.name LIKE '%$query%'
              OR category.name LIKE '%$query%'
              OR gender.gender LIKE '$query%'";
        return $this->db->fetch_array($sql);
    }
    public function searchCategories($query) {
        $sql = "SELECT * FROM category WHERE name LIKE '%$query%'";
        return $this->db->fetch_array($sql);
    }
    public function searchCollections($query) {
        $sql = "SELECT * FROM collection WHERE name LIKE '%$query%'";
        return $this->db->fetch_array($sql);
    }
    public function getLatestProducts() {
        $sql = "SELECT * FROM product WHERE deleted = 0 ORDER BY created_at DESC LIMIT 6";
        return $this->db->fetch_array($sql);
    }
    public function getProductsMen() {
        $sql = "SELECT * FROM product WHERE deleted = 0 AND gender = 2 ORDER BY created_at";
        return $this->db->fetch_array($sql);
    }
    public function getProductsWomen() {
        $sql = "SELECT * FROM product WHERE deleted = 0 AND gender = 1 ORDER BY created_at";
        return $this->db->fetch_array($sql);
    }
    public function getRandomProducts() {
        $sql = "SELECT * FROM product WHERE deleted = 0 ORDER BY RAND() LIMIT 15";
        return $this->db->fetch_array($sql);
    }
    public function getProductsByCategory($collection, $category) {
    	$params = array(
    		'%'.$collection.'%',
		    $category
        );
        $sql = "SELECT * FROM product WHERE collection_id LIKE ? AND category_id = ?";
        return $this->db->fetch_array($sql, $params);
    }
    public function delete($id){
        $sql = "UPDATE product SET deleted = 1 WHERE id = $id";
        return $this->db->query($sql);
    }
    public function save($id, $fileDestination){
                if (!empty($_FILES['file'])) {
                $file = $_FILES['file'];

                    $fileName = $file['name'];
                    $fileTmpName = $file['tmp_name'];
                    $fileSize = $file['size'];
                    $fileError = $file['error'];
                    $fileType = $file['type'];

                    $fileExt = explode('.', $fileName);
                    $fileActualExt = strtolower(end($fileExt));

                    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                    $fileDestinationWithName = $fileDestination . $fileNameNew;

                    $allowed = array('jpg', 'png', 'gif', 'jpeg');

                    if (in_array($fileActualExt, $allowed)) {
                        //Checks for errors
                        if ($fileError === 0) {

                            //Move raw file
                            move_uploaded_file($fileTmpName, DOCROOT . $fileDestinationWithName);

                            $params = array(
                                $this->name,
                                $this->description,
                                $this->collection,
                                $this->category,
                                $this->gender,
                                $fileNameNew
                            );

                            $sql = "INSERT INTO product(name, description, collection_id, category_id, gender, thumbnail) VALUES (?,?,?,?,?,?)";

                            $this->db->query($sql, $params);

                            /* Return new id */
                            return $this->db->getinsertid();

                            header('Location:' . DOCROOT . 'cms/products.php');

                            } else {
                                echo "Fejl i uploading af fil: " . $_FILES["file"]["error"];
                            }
                        } else {
                        echo "Du kan ikke uploade denne type filer";
                        }
                    }
            }
        }
