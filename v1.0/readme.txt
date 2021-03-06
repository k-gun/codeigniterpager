CodeIgniter Pager

How to work with it?

1. First, you need to place it to ./application/libraries/pager.php

2. To initialize, load it to wherever it will be used just before DB query which will be paged. It needs at least $count parameter to start process.

$cfg['count'] = $this->db->count_all('table');
$this->load->library('pager', $cfg);

Now, it is initialized and its $offset and $limit variables are ready to work in our DB transactions.

So, you can use these vars like this;

$sql = 'SELECT * FROM `table` LIMIT '.
        $this->pager->offset .', '. $this->pager->limit;
$data['query'] = $this->db->query($sql);

// produces SELECT * FROM `table` LIMIT 0, 10

Or like this;

$data['query'] = $this->db->limit(
                 $this->pager->limit, $this->pager->offset)->get('table');

3. Calling the generate() method. You can call that method with echoing it where all these generated links will be displayed;

echo $this->pager->generate();

Or, simply load the links to a variable to use them later;

$data['pages'] = $this->pager->generate();

You can also pass to this method 3 optional params, named as $links, $ignore, and $format.

$links: It provides how much numbered links will be displayed in the loop. For example, if $links = 3 displays << < 1 2 3 > >>. Default value is 5. Must be less than $count value (if not, function returns all links).

IMPORTANT! If you want to keep the current page number in the middle of displayed numbers, then $links value must be an 'odd' number like 3, 5, 11.

$ignore: It contains which segments will be ignored while generating the new links and accepts uniq or much params as array. For example, $ignore = 'index' will not keep �index� segment in the new links. Another usage type could be like that; $ignore = array('index', 'foo', 'bar', ...). Default value is null.

$format: As default, the generated links are kept in an array like below;

Pager::pages['nums'] = '<span class="current">1</span>';' . '  ' .
                       '<a href="/index/page/2">2</a>';' . '  ' .
                       ...
Pager::pages['next'] = '<a href="/index/page/2" class="next">></a>';
Pager::pages['last'] = '<a href="/index/page/10" class="last">>></a>';

And these links will be served as imploded so (not as array); 1 2 ... > >>

If you don�t prefer to use that data as imploded format, then you can set $format param as false to prevent implode command, and use the return like this;

$this->pager->generate(null, null, false);
$last_link = $this->pager->pages['last'];

4. Styling is very simple! If you want to stylize links, just use it like below;

<div class="pager"><?php echo $this->pager->generate(); ?></div>

// and CSS
.pager a {
   display: block;
   float: left;
   padding: 3px 6px;
   margin-right: 3px;
   border: 1px solid #eee;
   ...
}
.pager .current {
   font-weight: bold;
   ...
}

/* links also contains .first, .prev, .next, .last classes */
.pager a.first {
   ...
}

Optional parameters

1. Before initializing

1. To set up fetching limitation, $limit var will be used. Default value is 10.
2. To define page segment, $segment var will be used. Default value is 'page'.

$cfg['count'] = $this->db->count_all('table');
$cfg['limit'] = 5;
$cfg['segment'] = 'per_page';
$this->load->library('pager', $cfg);

IMPORTANT! Page segment needs a function like index() or other functions in classes. For example, if segment name is 'page', then needs a function which is named as 'page' and returns index() or returns which function is related with Pager else.

class Blog extends Controller {
   function index() {
      $cfg['count'] = $this->db->count_all('blog');
      $cfg['limit'] = 15;
      $cfg['segment'] = 'page';
      $this->load->library('pager', $cfg);

      $data['query'] = $this->db->limit($this->pager->limit, $this->pager->offset)->get('blog');
      return $data['query'];
   }

   function page() {
      return $this->index();
   }

2. After initializing

To configure some display options, you can change some default parameters using Pager::tools variable.

$this->pager->tools['first'] = 'First';
$this->pager->tools['prev'] = 'Prev';
$this->pager->tools['next'] = 'Next';
$this->pager->tools['last'] = 'Last';

// if 'last' or 'prev' etc. is unneeded, just blank it or set as null
$this->pager->tools['first'] = '';
// or
$this->pager->tools['prev'] = null;

// also can pass images
$this->pager->tools['next'] = '<img src=/images/right-arrow.gif' alt="Next" />

// get links
$pages = $this->pager->generate();

That�s it!

Last thing; Actually I�m not familiar with preparing documents like this and thus if there is any mistake or lack, please do not hesitate to write to me or to comment to post.
qeremy[at]gmail[dot]com

Thanks for reading article, using program.