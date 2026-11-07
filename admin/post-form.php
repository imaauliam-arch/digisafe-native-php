<?php
require_once __DIR__ . '/_init.php';
$id=(int)($_GET['id']??0); $editing=$id>0; $post=null;
if($editing){$s=$pdo->prepare('SELECT * FROM ds_posts WHERE id=?');$s->execute([$id]);$post=$s->fetch();if(!$post){http_response_code(404);exit('Artikel tidak ditemukan.');}}
$categories=$pdo->query('SELECT id,name FROM ds_categories ORDER BY name')->fetchAll();
$tagNames=[];if($editing){$s=$pdo->prepare('SELECT t.name FROM ds_tags t JOIN ds_post_tags pt ON pt.tag_id=t.id WHERE pt.post_id=?');$s->execute([$id]);$tagNames=array_column($s->fetchAll(),'name');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!verify_csrf())$error='Sesi tidak valid. Muat ulang halaman.';
 else{
  $title=trim($_POST['title']??'');$author=trim($_POST['author']??'Admin DIGISAFE');$excerpt=trim($_POST['excerpt']??'');$content=trim($_POST['content']??'');$category=(int)($_POST['category_id']??0);$tags=array_values(array_unique(array_filter(array_map('trim',explode(',',$_POST['tags']??'')))));$alt=trim($_POST['image_alt']??'');$source=trim($_POST['image_source_name']??'DIGISAFE');$sourceUrl=trim($_POST['image_source_url']??'');$status=($_POST['status']??'published')==='draft'?'draft':'published';
  if($title===''||$excerpt===''||$content===''||$author===''||$category<1||count($tags)<2||$alt==='')$error='Lengkapi semua kolom wajib dan masukkan minimal dua tag.';
  elseif($sourceUrl!==''&&!filter_var($sourceUrl,FILTER_VALIDATE_URL))$error='URL sumber gambar tidak valid.';
  else{
   $imagePath=$post['featured_image']??'';
   if(!empty($_FILES['featured_image']['name'])){$f=$_FILES['featured_image'];$info=$f['error']===UPLOAD_ERR_OK?@getimagesize($f['tmp_name']):false;$mime=$info['mime']??'';$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];if(!$info||!isset($allowed[$mime])||$f['size']>3*1024*1024)$error='Gambar harus JPG/PNG/WEBP dan maksimal 3 MB.';else{$name=bin2hex(random_bytes(16)).'.'.$allowed[$mime];if(!move_uploaded_file($f['tmp_name'],__DIR__.'/../uploads/'.$name))$error='Gagal menyimpan gambar.';else $imagePath='uploads/'.$name;}}
   elseif(!$editing)$error='Pilih gambar unggulan artikel.';
   if(!$error){try{$pdo->beginTransaction();$slugBase=strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/','-',iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$title)),'-'));$slug=($slugBase?:'artikel').'-'.bin2hex(random_bytes(3));$htmlContent=nl2br(e($content));
    if($editing){$q=$pdo->prepare("UPDATE ds_posts SET category_id=?,title=?,excerpt=?,content=?,author_name=?,featured_image=?,image_alt=?,image_source_url=?,image_source_name=?,status=?,published_at=IF(?=1 AND status<>'published',NOW(),published_at) WHERE id=?");$q->execute([$category,$title,$excerpt,$htmlContent,$author,$imagePath,$alt,$sourceUrl?:'#',$source,$status,$status==='published'?1:0,$id]);$pdo->prepare('DELETE FROM ds_post_tags WHERE post_id=?')->execute([$id]);$postId=$id;$activity='post_update';$desc='Memperbarui artikel: '.$title;
    }else{$q=$pdo->prepare('INSERT INTO ds_posts (category_id,title,slug,excerpt,content,author_name,featured_image,image_alt,image_source_url,image_source_name,status,published_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())');$q->execute([$category,$title,$slug,$excerpt,$htmlContent,$author,$imagePath,$alt,$sourceUrl?:'#',$source,$status]);$postId=(int)$pdo->lastInsertId();$activity='post_create';$desc='Mengunggah artikel: '.$title;}
    foreach($tags as $tag){$tagSlug=strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/','-',iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$tag)),'-'))?:'tag-'.bin2hex(random_bytes(3));$q=$pdo->prepare('INSERT INTO ds_tags(name,slug) VALUES(?,?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)');$q->execute([$tag,$tagSlug]);$tagId=(int)$pdo->lastInsertId();$pdo->prepare('INSERT IGNORE INTO ds_post_tags(post_id,tag_id) VALUES(?,?)')->execute([$postId,$tagId]);}
    $pdo->commit();log_activity($pdo,$activity,$desc,'post',$postId);$_SESSION['admin_flash']=$editing?'Artikel berhasil diperbarui.':'Artikel berhasil dipublikasikan.';header('Location: posts.php');exit;
   }catch(Throwable $ex){if($pdo->inTransaction())$pdo->rollBack();error_log('DIGISAFE post save: '.$ex->getMessage());$error='Artikel gagal disimpan. Periksa kembali database.';}}
  }
 }
}
admin_header($editing?'Edit Artikel':'Tambah Artikel','posts');admin_flash();
?>
<div class="admin-panel form-panel"><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<label>Judul artikel *</label><input name="title" required maxlength="200" value="<?=e($_POST['title']??$post['title']??'')?>">
<label>Penulis *</label><input name="author" required maxlength="100" value="<?=e($_POST['author']??$post['author_name']??'Admin DIGISAFE')?>">
<label>Kategori *</label><select name="category_id" required><option value="">Pilih kategori</option><?php foreach($categories as $c):?><option value="<?=$c['id']?>" <?=((int)($_POST['category_id']??$post['category_id']??0)===(int)$c['id'])?'selected':''?>><?=e($c['name'])?></option><?php endforeach;?></select>
<label>Ringkasan *</label><textarea name="excerpt" rows="3" required><?=e($_POST['excerpt']??$post['excerpt']??'')?></textarea>
<label>Isi artikel *</label><textarea name="content" rows="12" required><?=e($_POST['content']??strip_tags(str_replace(['<br>','<br />'],"\n",$post['content']??'')))?></textarea>
<label>Tag * (minimal 2, pisahkan dengan koma)</label><input name="tags" required value="<?=e($_POST['tags']??implode(', ',$tagNames))?>">
<label>Gambar unggulan <?= $editing?'(kosongkan jika tidak diganti)':'*' ?></label><input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp" <?= $editing?'':'required' ?>>
<label>Alt text gambar *</label><input name="image_alt" required maxlength="255" value="<?=e($_POST['image_alt']??$post['image_alt']??'')?>">
<label>Nama sumber gambar</label><input name="image_source_name" value="<?=e($_POST['image_source_name']??$post['image_source_name']??'DIGISAFE')?>">
<label>URL sumber gambar</label><input type="url" name="image_source_url" value="<?=e($_POST['image_source_url']??(($post['image_source_url']??'#')==='#'?'':$post['image_source_url']??''))?>">
<label>Status artikel</label><select name="status"><option value="published" <?=($post['status']??'published')==='published'?'selected':''?>>Published</option><option value="draft" <?=($post['status']??'')==='draft'?'selected':''?>>Draft</option></select>
<div class="form-buttons"><button class="btn btn-primary" type="submit">Simpan Artikel →</button><a class="btn btn-light" href="posts.php">Batal</a></div></form></div>
<?php admin_footer(); ?>
