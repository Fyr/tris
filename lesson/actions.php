<?
class LessonActions {

	private $View;

	private $lessonID, $userID;
	private $chapterModel, $paraModel, $snippetModel, $snipOptsModel, $postModel, $noteModel, $mediaModel, $visitedModel;
	private $lessonModel;

	public function __construct($lessonID, $userID) {
		$this->View = new View();

		if ($lessonID) {
			$this->setID($lessonID);
		}
		$this->userID = $userID;

		$this->chapterModel = LessonModel::getModel('chapters');
		$this->paraModel = LessonModel::getModel('paragraphs');
		$this->snippetModel = LessonModel::getModel('snippets');
		$this->snipOptsModel = LessonModel::getModel('snippet_options');
		$this->postModel = LessonModel::getModel('posts');
		$this->noteModel = LessonModel::getModel('notes');
		$this->mediaModel = LessonModel::getModel('media');
		$this->visitedModel = LessonModel::getModel('visited');
		$this->lessonModel = LessonModel::getModel('lessons');
	}

	/* TODO:
	public function runAction($action, $data = array()) {
		if (method_exists($this, $action)) {
			return array('status' => 'ERROR', 'errMsg' => 'Вызов несуществующего метода');
		}
	}
	*/
	public function checkAccess() {
		return $this->lessonModel->checkUserAccess($this->lessonID, $this->userID);
	}

	protected function set($key, $value) {
		$this->View->set($key, $value);
	}

	protected function render($template) {
		return $this->View->render(BASE_DIR.'view/'.$template.'.php');
	}

	public function setID($id) {
		$this->lessonID = $id;
	}

	public function lessonInit() {
		$this->chapterUpdate(array('title' => 'Глава 1'));
	}

	public function lessonUpdate($data) {
		$oldLessonID = $this->lessonID;
		$this->lessonID = $data['id'];
		$this->lessonModel->save(array('id' => $this->lessonID, 'title' => $data['title']));
		if (!$this->lessonID) {
			$this->lessonInit();
		}
		$response = array('status' => 'OK', 'thumbHTML' => $this->getThumbContent(true));
		$this->lessonID = $oldLessonID;
		return $response;
	}

	public function chapterUpdate($data) {
		$data['lesson_id'] = $this->lessonID;
		$id = $this->chapterModel->save($data);

		if (!(isset($data['id']) && $data['id'])) { // add new chapter
			// auto-add a new paragraph
			$this->paraInit($id);
		}
		// Set default sort order
		$data = array('id' => $id, 'sort_order' => $id);
		$id = $this->chapterModel->save($data);
		return array('status' => 'OK');
	}

	public function chapterDelete($data) {
		$paragraps = $this->paraModel->findAll(array('chapter_id' => $data['id']));
		foreach($paragraps as $row) {
			$this->paraDelete(array('id' => $row['id']));
		}
		// $this->paraDelete(array('chapter_id' => $data['id']));

		$this->chapterModel->delete($data['id']);
		return array('status' => 'OK');
	}

	public function getChapters() {
		return $this->chapterModel->findAll(array('lesson_id' => $this->lessonID), 'sort_order');
	}

	public function chapterSort($data) {
		$this->chapterModel->sortMove($data['id'], $data['dir'], 'lesson_id = '.$this->lessonID);
		return array('status' => 'OK');
	}

	public function getChaptersContent($lEditMode = true, $currPara = false) {
		$aRowset = $this->paraModel->getParagraphList(array('lesson_id' => $this->lessonID));
		$aChapters = array();
		foreach($aRowset as $row) {
			$aChapters[$row['chapter_id']] = array('id' => $row['chapter_id'], 'title' => $row['chapter_title'], 'lesson_id' => $row['lesson_id']);
		}

		$aParagraphs = array();
		foreach ($aRowset as $row) {
			$aParagraphs[$row['chapter_id']][] = $row;
		}
		$this->set('aChapters', $aChapters);
		$this->set('aParagraphs', $aParagraphs);
		$this->set('currPara', $currPara);
		return $this->render(($lEditMode) ? 'view_chapters_editmode' : 'view_chapters');
	}

	public function paraInit($chapterID) {
		return $this->paraUpdate(array('chapter_id' => $chapterID, 'title' => 'Название параграфа'));
	}

	public function paraUpdate($data) {
		$id = $this->paraModel->save($data);

		// Set default values
		if (!(isset($data['id']) && $data['id'])) { // add new paragraph
			$sampleContent = '<div class="container-scroll-box"><div class="container-scroll-box-holder"> <h2>'.$data['title'].'</h2><p>Здесь должен быть какой-то текст для &laquo;'.$data['title'].'&raquo;...</p> </div></div>';
			$data = array('id' => $id, 'sort_order' => $id, 'content' => $sampleContent, 'content_cached' => $sampleContent);
			$this->paraModel->save($data);
		}
		return array('status' => 'OK', 'aParaInfo' => $this->paraModel->getParagraphList(array('lesson_id' => $this->lessonID)));
	}

	public function paraDelete($data) {
		// Delete media files
		$this->audioDelete($data);

		// Delete notes and posts
		$this->noteModel->deleteAll(array('para_id' => $data['id']));
		$this->postModel->deleteAll(array('para_id' => $data['id']));

		// Delete snippets and its options
		$this->snippetModel->deleteSnippetOptions($data['id']);
		$this->snippetModel->deleteAll(array('paragraph_id' => $data['id']));

		// Delete stats
		$this->visitedModel->deleteAll(array('para_id' => $data['id']));

		$this->paraModel->delete($data['id']);
		return array('status' => 'OK');
	}

	public function paraSort($data) {
		$row = $this->paraModel->getItem($data['id']);
		$this->paraModel->sortMove($data['id'], $data['dir'], 'chapter_id = '.$row['chapter_id']);
		return array('status' => 'OK');
	}

	public function reorder($data) {
		foreach ($data['chapter']['sort_order'] as $id => $sort_order) {
			$this->chapterModel->save(compact('id', 'sort_order'));
		}
		foreach ($data['para']['sort_order'] as $id => $sort_order) {
			$this->paraModel->save(compact('id', 'sort_order'));
		}
		return array('status' => 'OK');
	}

	private function parseSubheaders($content) {
		$pos = $pos2 = 0;
		$key = '<span class="subheader">';
		$aTitles = array();
		$count = 0;
		while (($pos = mb_strpos($content, $key, $pos2)) !== false) {
			$count++;
			$pos2 = mb_strpos($content, '</span>', $pos);
			$aTitles['sub_'.$count] = mb_substr($content, $pos + mb_strlen($key), $pos2 - $pos - mb_strlen($key));
		}
		return $aTitles;
	}

	private function postProcessSubheaders($content) {
		$count = 0;
		while (strpos($content, 'sub_id') !== false) {
			$count++;
			$content = preg_replace('/sub_id/', 'sub_'.$count, $content, 1);
		}
		return $content;
	}

	private function postProcessQuiz($content) {
		$pos = $pos2 = 0;
		$key = '<span class="quiz-right-answer">';
		$aTitles = array();
		$count = 0;
		while (($pos = mb_strpos($content, $key, $pos)) !== false) {
			$count++;
			$pos2 = mb_strpos($content, '</span>', $pos);
			$pos+= mb_strlen($key);
			$content = mb_substr($content, 0, $pos).mb_substr($content, $pos2);
		}
		$content = str_replace('data-quiz_onsubmit=""', 'onclick="quizSubmit(this)"', $content);
		$content = str_replace('<span class="quiz-answer-header">', '<span class="quiz-answer-header" style="display: none;">', $content);
		return $content;
	}

	public function saveContent($_data) {
		// Save content
		$data = array('id' => $_GET['p'], 'content' => trim(stripslashes($_data['mercury-content']['value'])) );
		$this->paraModel->save($data);

		// Save snippets
		$this->snippetModel->deleteSnippetOptions($data['id']);
		$this->snippetModel->deleteAll(array('paragraph_id' => $data['id']));
		if (isset($_data['mercury-content']['snippets'])) {
			foreach ($_data['mercury-content']['snippets'] as $snippet_id => $snippet) {
				// $id = str_replace('snippet_', '', $snippet_id);
				$id = $this->snippetModel->save(array(
					'paragraph_id' => $data['id'],
					'snippet_key' => $snippet['name'],
					'snippet_id' => str_replace('snippet_', '', $snippet_id)
				));
				unset($snippet['name']);
				unset($snippet['edit']);
				foreach($snippet as $option => $value) {
					$this->snipOptsModel->save(array('snippet_id' => $id, 'option_key' => $option, 'value'  => $value));
				}
			}
		}

		$content_cached = trim($this->renderContent($data['id'], false));
		$aTitles = $this->parseSubheaders($content_cached);
		$content_cached = $this->postProcessSubheaders($content_cached);

		$content_cached = $this->postProcessQuiz($content_cached);

		$this->paraModel->save(array('id' => $data['id'], 'content_cached' => $content_cached, 'subheaders' => ($aTitles) ? serialize($aTitles) : ''));
		return array('status' => 'OK');
	}

	public function renderContent($paraID, $lEdit = false) {
		$para = $this->paraModel->getItem($paraID);
		if (!$para) {
			return '';
		}
		$content = $para['content'];

		$aOptions = $this->getSnippetOptions($paraID);
		foreach ($aOptions as $snippet_id => $options) {
			// $path = './mercury/snippets/'.$options['_snippet_name'].'/preview.php';
			$snippet = $options['_snippet_name'];
			unset($options['_snippet_name']);

			$_POST = $options;
			$_POST['edit'] = $lEdit;
			$_POST['snippet_id'] = $snippet_id;

			// Init snippet action class
			$basePath = './mercury/snippets/'.$snippet.'/';
			$html = initSnippetResponse($snippet, 'preview', $basePath, $paraID);

			// Strip <script> content as it causes Mercury error
			$pos = strpos($html, '<script');
			if ($pos !== false) {
				$pos2 = strpos($html, '</script>');
				$html = substr($html, 0, $pos).substr($html, $pos2 + strlen('</script>'));
			}
			// $content = str_replace('['.$snippet_id.'/1]', $html, $content);
			$content = preg_replace('/\['.$snippet_id.'\/(\d+)\]/i', $html, $content);
		}
		return $content;
	}

	public function getSnippetOptions($paraID) {
		$options = $this->snippetModel->getSnippetOptions($paraID);
		$aOptions = array();
		$aSnippets = array();
		foreach ($options as $row) {
			$id = 'snippet_'.$row['snippet_id'];
			$aSnippets[$id] = $row['snippet_key'];
			if ($row['option_key']) {
				$aOptions[$id][$row['option_key']] = $row['value'];
			} else {
				$aOptions[$id] = array();
			}
		}
		foreach($aOptions as $id => &$option) {
			$option['_snippet_name'] = $aSnippets[$id];
		}
		return $aOptions;
	}

	public function setFavorite($data) {
		$paraID = $data['id'];
		$para = $this->paraModel->save(array('id' => $paraID, 'favorite' => $data['favorite']));

		$aFavoriteInfo = $this->getFavoriteContent();
		return array('status' => 'OK', 'favoriteHTML' => $aFavoriteInfo['html'], 'favCount' => $aFavoriteInfo['count']);
	}

	public function getFavoriteContent() {
		$aFavorite = $this->paraModel->getParagraphList(array('lesson_id' => $this->lessonID, 'favorite' => true));
		$this->set('aFavorite', $aFavorite);
		return array('html' => $this->render('view_favorite'), 'count' => (is_array($aFavorite)) ? count($aFavorite) : 0);
	}

	public function getSearchContent($q = '') {
		$aResults = array();
		if ($q) {
			$aResults = $this->paraModel->search($q, $this->lessonID);
		}
		$this->set('aResults', $aResults);
		return array('html' => $this->render('view_search'), 'count' => (is_array($aResults)) ? count($aResults) : 0);
	}

	public function search($data) {
		$aSearchInfo = $this->getSearchContent($data['q']);
		return array('status' => 'OK', 'searchHTML' => $aSearchInfo['html'], 'searchCount' => $aSearchInfo['count']);
	}

	private function isPostValid($data) {
		$aFields = array();
		/*
		if (!(isset($data['username']) && $data['username'])) {
			$aFields['username'] = 'Поле не может быть пустым';
		} elseif (mb_strlen($data['username']) <= 2) {
			$aFields['username'] = 'Имя пользователя должно быть не менее 3х символов';
		}
		if (!(isset($data['email']) && $data['email'])) {
			$aFields['email'] = 'Поле не может быть пустым';
		}
		*/
		if (!(isset($data['body']) && $data['body'])) {
			$aFields['body'] = 'Поле не может быть пустым';
		}
		return $aFields;
	}

	public function post($data) {
		$errMsg = '';
		$aErrFields = $this->isPostValid($data);
		if (!$this->userID) {
			$errMsg = 'Оставлять комментарии могут только авторизованные пользователи';
		} elseif ($aErrFields) { // post comment
			$errMsg = 'Ошибка заполнения формы отправки комментария';
		} else {
			//if (isset($data['id']))
			$data['created'] = $data['updated'] = date('Y-m-d H:i:s');
			$data['user_id'] = $this->userID;
			$this->postModel->save($data);
		}

		$aPostInfo = $this->getPostsContent($data['para_id']);
		return array(
			'status' => ($errMsg) ? 'ERROR' : 'OK',
			'errMsg' => $errMsg,
			'errFields' => $aErrFields,
			'postsHTML' => $aPostInfo['html'],
			'postsCount' => $aPostInfo['count']
		);
	}

	public function getPostsContent($paraID) {
		$aResults = $this->postModel->getPosts($paraID);
		$user_ID = $this->userID;

		$this->set('aResults', $aResults);
		$this->set('user_ID', $user_ID);
		return array('html' => $this->render('view_posts'), 'count' => (isset($aResults['Post'])) ? count($aResults['Post']) : 0);
	}

	public function postUpdate($data) {
		$errMsg = '';
		$post = $this->postModel->getItem($data['id']);
		if (!$this->userID) {
			$errMsg = 'Оставлять комментарии могут только авторизованные пользователи';
		} elseif ($post['user_id'] != $this->userID) {
			$errMsg = 'Авторизованные пользователи могут редактировать только свои комментарии';
		} else {
			$data['updated'] = date('Y-m-d H:i:s');
			$this->postModel->save($data);
		}
		$aPostInfo = $this->getPostsContent($post['para_id']);
		return array(
			'status' => ($errMsg) ? 'ERROR' : 'OK',
			'errMsg' => $errMsg,
			'errFields' => $aErrFields,
			'postsHTML' => $aPostInfo['html'],
			'postsCount' => $aPostInfo['count']
		);
	}

	public function postDelete($data) {
		$post = $this->postModel->getItem($data['id']);
		$this->postModel->delete($data['id']);
		$aPostInfo = $this->getPostsContent($post['para_id']);
		return array(
			'status' => 'OK',
			'postsHTML' => $aPostInfo['html'],
			'postsCount' => $aPostInfo['count']
		);
	}

	public function note($data) {
		$data['created'] = $data['updated'] = date('Y-m-d H:i:s');
		$data['user_id'] = $this->userID;
		$this->noteModel->save($data);

		$notesInfo = $this->getNotesContent($data['para_id']);
		return array(
			'status' => 'OK',
			'notesHTML' => $notesInfo['html'],
			'notesCount' => $notesInfo['count']
		);
	}

	public function getNotesContent($paraID) {
		$aResults = $this->noteModel->getNotes($this->userID);
		$notesCount = (is_array($aResults)) ? count($aResults) : 0;

		$aNotes = array();
		foreach($aResults as $i => $row) {
			if ($row['para_id'] == $paraID) {
				$aNotes[] = $row;
				unset($aResults[$i]);
			}
		}
		// $lessonID = $this->lessonID;
		$this->set('aNotes', $aNotes);
		$this->set('aResults', $aResults);
		return array('html' => $this->render('view_notes'), 'count' => $notesCount);
	}

	public function noteUpdate($data) {
		$paraID = $data['para_id'];
		unset($data['para_id']);
		$data['updated'] = date('Y-m-d H:i:s');
		$this->noteModel->save($data);

		$notesInfo = $this->getNotesContent($paraID);
		return array(
			'status' => 'OK',
			'notesHTML' => $notesInfo['html'],
			'notesCount' => $notesInfo['count']
		);
	}

	public function noteDelete($data) {
		$note = $this->noteModel->getItem($data['id']);
		$this->noteModel->delete($data['id']);

		$notesInfo = $this->getNotesContent($data['para_id']);
		return array(
			'status' => 'OK',
			'notesHTML' => $notesInfo['html'],
			'notesCount' => $notesInfo['count']
		);
	}

	public function audioUpload($data) {
		if (isset($_FILES['type']) && !in_array($_FILES['type'], array('audio/wav'))) {
			return array('status' => 'ERROR', 'errMsg' => 'Неверный формат аудио-файла');
		}
		if ($file = $this->mediaModel->uploadFile('audio', AUDIO_DIR, 'lesson_'.$this->lessonID.'_para_'.$data['id'])) {
			$this->mediaModel->save(array('media_type' => 'audio', 'object_id' => $data['id'], 'file' => $file));
			return array('status' => 'OK', 'file' => AUDIO_DIR.$file);
		}
		return array('status' => 'ERROR', 'errMsg' => 'Ошибка загрузки аудио-файла');
	}

	public function audioDelete($data) {
		return $this->mediaModel->delMediaItem($data['id']);
	}

	public function updateVisited($paraID) {
		$data = array('user_id' => $this->userID, 'para_id' => $paraID, 'last_visited' => date('Y-m-d H:i:s'));
		if ($row = $this->visitedModel->findOne(array('user_id' => $this->userID, 'para_id' => $paraID))) {
			$data['id'] = $row['id'];
		}
		$this->visitedModel->save($data);
	}

	public function getLessonInfo($lessonID) {
		return $this->lessonModel->getLessonInfo($lessonID);
	}

	public function getThumbInfo($lEditMode = false) {
		$lastVisited = $this->paraModel->getLastVisited($this->userID, null, $lEditMode);
		$aThumbs = $this->mediaModel->getThumbsList();
		return array('LastVisited' => $lastVisited, 'Thumb' => $aThumbs);
	}

	public function getThumbContent($lEditMode = false) {
		$thumbInfo = $this->getThumbInfo($lEditMode);
		$this->set('thumbInfo', $thumbInfo);
		$this->set('lEditMode', $lEditMode);
		return $this->render('view_thumbs');
	}

	public function switchPara($data) {
		$paraID = $data['id'];
		$para = $this->paraModel->getItem($paraID);

		$contentHTML = $para['content_cached'];
		$this->set('contentHTML', $contentHTML);
		$html = $this->render('view_content');

		$this->updateVisited($paraID);

		// update state for top icons
		// update state for needed panels
		$postInfo = $this->getPostsContent($paraID);
		$notesInfo = $this->getNotesContent($paraID);
		return array(
			'status' => 'OK',
			'favorite' => $para['favorite'],
			'content' => $html,
			'postsHTML' => $postInfo['html'],
			'postsCount' => $postInfo['count'],
			'notesHTML' => $notesInfo['html'],
			'notesCount' => $notesInfo['count']
		);
	}

	public function quizAnswer($data) {
		$conditions = array('paragraph_id' => $data['para_id'], 'snippet_key' => 'quiz', 'snippet_id' => $data['snippet_id']);
		$snippet = $this->snippetModel->findOne($conditions);
		if ($snippet) {
			$quizModel = new LessonModel('snippet_quiz');
			$yourAnswer = '';
			if (isset($data['body'])) {
				$yourAnswer = $data['body'];
				$data = array('user_id' => $this->userID, 'snippet_id' => $snippet['id'], 'body' => $yourAnswer);
				$data['created'] = $data['updated'] = date('Y-m-d H:i:s');
				$quizModel->save($data);
			} else {
				$conditions = array('user_id' => $this->userID, 'snippet_id' => $snippet['id']);
				$answer = $quizModel->findOne($conditions, 'created');
				if ($answer && $answer['body']) {
					$yourAnswer = $answer['body'];
				}
			}

			if (!$yourAnswer) {
				return array('status' => 'OK', 'answers' => '');
			}

			// Get right answer from snippet options
			$conditions = array('snippet_id' => $snippet['id'], 'option_key' => 'answer');
			$answer = $this->snipOptsModel->findOne($conditions);

			// Get answers of other users
			$userAnswers = $quizModel->findAll(array('snippet_id' => $snippet['id'], 'user_id <> '.$this->userID), 'created');
			$aID = array();
			foreach($userAnswers as $row) {
				$aID[] = $row['user_id'];
			}

			$aUserAnswers = array('Answer' => $userAnswers, 'User' => $this->lessonModel->getUsersList(array('u.ID' => $aID)));
			$rightAnswer = $answer['value'];

			$this->set('rightAnswer', $rightAnswer);
			$this->set('yourAnswer', $yourAnswer);
			$this->set('aUserAnswers', $aUserAnswers);
			return array('status' => 'OK', 'answers' => $this->render('view_quiz_answers'));
		}
		return array('status' => 'ERROR', 'errMsg' => 'Некорректный ID quiz-сниппета:'.print_r($conditions, true));
	}

	public function lessonImageUpload($data) {
		if (isset($_FILES['type']) && !in_array($_FILES['type'], array('image/jpeg', 'image/jpg', 'image/png', 'image/gif'))) {
			return array('status' => 'ERROR', 'errMsg' => 'Неверный формат файла');
		}

		$response = $this->mediaModel->uploadMedia('media', 'image', 'LessonThumb', $data['id']);
		if ($response['status'] == 'OK') {
			$response['thumbHTML'] = $this->getThumbContent(true);
			return $response;
		}

		return array('status' => 'ERROR', 'errMsg' => 'Ошибка загрузки файла');
	}

	public function lessonDelete($data) {
		$aMedia = $this->mediaModel->getMediaItemList(null, array('Lesson', 'LessonThumb'), $data['id']);
		if (count($aMedia)) {
			foreach($aMedia as $mediaType) {
				foreach($mediaType as $media) {
					$response = $this->mediaModel->delMediaItem($media['id']);
					if ($response['status'] == 'ERROR') {
						return $response;
					}
				}
			}
		}
		$chapters = $this->chapterModel->findAll(array('lesson_id' => $data['id']));
		foreach($chapters as $row) {
			$this->chapterDelete(array('id' => $row['id']));
		}
		$this->lessonModel->delete($data['id']);
		return array('status' => 'OK', 'thumbHTML' => $this->getThumbContent(true));
	}

}