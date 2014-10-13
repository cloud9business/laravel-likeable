Laravel Likeable Plugin
============

#### *NOT STABLE*
Trait for Laravel Eloquent models to allow easy implementation of a "like" or "favorite" or "remember" feature.

#### Composer Install

    "require": {
        "cloud9business/eloquent-likeable": "0.1.*"
    }

#### Run the migrations

	php artisan migrate --package=cloud9business/eloquent-likeable
	
#### Setup your models

    class Article extends \Eloquent {
        use Cloud9Business\EloquentlLikeable\LikeableTrait;
    }

#### Sample Usage

    $article->like(); // like the article for current user
    $article->like($myUserId); // pass in your own user id
    $article->like(0); // just add likes to the count, and don't track by user
    
    $article->unlike(); // remove like from the article
    $article->unlike($myUserId); // pass in your own user id
    $article->unlike(0); // remove likes from the count -- does not check for user
    
    $article->likes; // get count of likes

    $article->liked(); // check if currently logged in user liked the article
    $article->liked($myUserId);
    
    Article::whereLiked($myUserId) // find only articles where user liked them
    	->with('likeCounter') // highly suggested to allow eager load
    	->get();
    
    
## Likeable Controller

Also this package provides `\Cloud9Business\EloquentlLikeable\LikeableController`, which handle requests to like entities

### Usage
Add the service provider to `app/config/app.php`

```php
'providers' => array(
    // providers...
    
    'Cloud9Business\EloquentlLikeable\LikeableServiceProvider',
)
```

publish the config:
 
```bash
php artisan config:publish cloud9business/eloquent-likeable
```

Add models you need to sort in the config `app/config/packages/cloud9business/eloquentl-likeable/config.php`:

```php
'entities' => array(
     'articles' => '\Article', // entityNameForUseInRequest => ModelName
),
```

Add route to the `like` method of the controller:

```php
Route::post('like', '\Cloud9Business\EloquentlLikeable\LikeableController@like'); 
```

Now if you post to this route valid data:

```php
$validator = \Validator::make(\Input::all(), array(
    'type' => array('required', 'in:like,unlike'), // type of like, like or unlike
    'entityName' => array('required', 'in:' . implode(',', array_keys($likeableEntities))), // entity name, 'articles' in this example
    'id' => 'required|numeric', // entity id
));
```

Then entity with `\Input::get('id')` id will be liked or unliked as specified.

For example, if request data is:

```
type:unlike
entityName:articles
id:3
```
then the article with id 3 will be unliked. 

#### Credits

 - Stephen Neander - http://www.cloud9business.com
 - Robert Conner - http://smartersoftware.net