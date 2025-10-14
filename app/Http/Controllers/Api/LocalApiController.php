<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\News;
use App\User;
use App\Event;
use App\Car;
use App\CountrySponsor;
use Illuminate\Support\Facades\DB;

/**
 * Local API Controller
 * Serves data from local database (synced from production)
 * Mimics production API endpoints for seamless frontend integration
 */
class LocalApiController extends Controller
{
    public $successStatus = 200;

    /**
     * HOME PAGE ENDPOINTS
     */
    
    // GET /api/top-three-judges
    public function topThreeJudges()
    {
        $judges = User::where('is_judge', 1)
            ->orderBy('victory_points', 'DESC')
            ->limit(3)
            ->get(['id', 'name', 'victory_points', 'country_id']);
        
        return response()->json([
            'data' => $judges,
            'success' => true
        ], $this->successStatus);
    }
    
    // GET /api/top-three-participants
    public function topThreeParticipants()
    {
        $participants = User::where('is_judge', 0)
            ->orderBy('victory_points', 'DESC')
            ->limit(3)
            ->get(['id', 'name', 'victory_points', 'country_id']);
        
        return response()->json([
            'data' => $participants,
            'success' => true
        ], $this->successStatus);
    }

    /**
     * NEWS ENDPOINTS
     */
    
    // GET /api/news-list-all?offset=0&limit=20&search=
    public function newsListAll(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);
        $search = $request->input('search', '');
        $country_id = $request->input('country_id', null);
        
        $query = News::select('news.*')
            ->orderBy('news.created_at', 'DESC');
        
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }
        
        if ($country_id !== null) {
            $query->where('country_id', $country_id);
        }
        
        $total = $query->count();
        $news = $query->offset($offset)->limit($limit)->get();
        
        return response()->json([
            'data' => $news,
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'success' => true
        ], $this->successStatus);
    }
    
    // GET /api/news-detail/{id}
    public function newsDetail($id)
    {
        $news = News::find($id);
        
        if (!$news) {
            return response()->json([
                'error' => true,
                'message' => 'News not found'
            ], 404);
        }
        
        return response()->json([
            'data' => $news,
            'success' => true
        ], $this->successStatus);
    }

    /**
     * SPONSORS ENDPOINTS
     */
    
    // GET /api/news-list-sponsor-tier?sponsor_type=0&sponsor_tier=2&country_id=1
    public function newsListSponsorTier(Request $request)
    {
        try {
            $sponsor_type = $request->input('sponsor_type', 0);
            $sponsor_tier = $request->input('sponsor_tier', 2);
            $country_id = $request->input('country_id', 0);
            $offset = $request->input('offset', 0);
            $limit = $request->input('limit', 10);
            
            // Build query with user relationship only (no userProfile to avoid null issues)
            $query = CountrySponsor::with('user');
            
            // Filter by sponsor_type_id
            if ($sponsor_type !== null && $sponsor_type !== '') {
                $query->where('sponsor_type_id', $sponsor_type);
            }
            
            // Filter by sponsor_tier_id
            if ($sponsor_tier !== null && $sponsor_tier !== '') {
                $query->where('sponsor_tier_id', $sponsor_tier);
            }
            
            // Filter by country_id
            if ($country_id > 0) {
                $query->where('country_id', $country_id);
            }
            
            // Order by updated_at desc
            $query->orderBy('updated_at', 'desc');
            
            // Get total count before pagination
            $total = $query->count();
            
            // Apply pagination and get results
            $sponsors = $query->offset($offset)->limit($limit)->get();
            
            // Transform data safely
            $transformedSponsors = [];
            foreach ($sponsors as $sponsor) {
                $item = [
                    'id' => $sponsor->id,
                    'user_id' => $sponsor->user_id,
                    'country_id' => $sponsor->country_id,
                    'sponsor_type_id' => $sponsor->sponsor_type_id ?? 0,
                    'sponsor_tier_id' => $sponsor->sponsor_tier_id ?? 0,
                    'updated_at' => $sponsor->updated_at ? $sponsor->updated_at->format('Y-m-d H:i:s') : null,
                    'created_at' => $sponsor->created_at ? $sponsor->created_at->format('Y-m-d H:i:s') : null,
                ];
                
                // Add user data if available
                if ($sponsor->user) {
                    $item['user'] = $sponsor->user->toArray();
                    $item['title'] = $sponsor->user->name ?? 'Sponsor';
                    // Use user avatar if available, otherwise placeholder
                    $item['thumbnail'] = $sponsor->user->avatar ?? '/assets/imgs/sponsor-placeholder.jpg';
                    // Get user name for author field
                    $item['author'] = $sponsor->user->name ?? 'Sponsor';
                    $item['user_avatar'] = $sponsor->user->avatar ?? '/assets/imgs/sponsor-placeholder.jpg';
                } else {
                    $item['title'] = 'Sponsor';
                    $item['thumbnail'] = '/assets/imgs/sponsor-placeholder.jpg';
                    $item['author'] = 'Sponsor';
                    $item['user_avatar'] = '/assets/imgs/sponsor-placeholder.jpg';
                }
                
                $transformedSponsors[] = $item;
            }
            
            return response()->json([
                'data' => $transformedSponsors,
                'total' => $total,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in newsListSponsorTier: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            return response()->json([
                'data' => [],
                'total' => 0,
                'offset' => 0,
                'limit' => 10,
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    // GET /api/news-list-sponsor-all
    public function newsListSponsorAll()
    {
        $sponsors = CountrySponsor::all();
        
        return response()->json([
            'data' => $sponsors,
            'success' => true
        ], $this->successStatus);
    }
    
    // GET /api/news-list-sponsor-id/{id}?offset=0&limit=3
    public function newsListSponsorId($id, Request $request)
    {
        try {
            $offset = $request->input('offset', 0);
            $limit = $request->input('limit', 10);
            
            // Get sponsor details by user_id
            $sponsor = CountrySponsor::with('user')
                ->where('user_id', $id)
                ->first();
            
            if (!$sponsor) {
                return response()->json([
                    'data' => null,
                    'success' => false,
                    'message' => 'Sponsor not found'
                ], 404);
            }
            
            // Get sponsor's news/articles
            $news = News::where('user_id', $id)
                ->orderBy('updated_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();
            
            $totalNews = News::where('user_id', $id)->count();
            
            // Transform sponsor data
            $sponsorData = [
                'id' => $sponsor->id,
                'user_id' => $sponsor->user_id,
                'country_id' => $sponsor->country_id,
                'sponsor_type_id' => $sponsor->sponsor_type_id ?? 0,
                'sponsor_tier_id' => $sponsor->sponsor_tier_id ?? 0,
            ];
            
            if ($sponsor->user) {
                $sponsorData['name'] = $sponsor->user->name ?? 'Sponsor';
                $sponsorData['email'] = $sponsor->user->email ?? '';
                $sponsorData['avatar'] = $sponsor->user->avatar ?? '/assets/imgs/sponsor-placeholder.jpg';
            }
            
            return response()->json([
                'sponsor' => $sponsorData,
                'news' => $news,
                'total_news' => $totalNews,
                'success' => true
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error in newsListSponsorId: ' . $e->getMessage());
            
            return response()->json([
                'sponsor' => null,
                'news' => [],
                'total_news' => 0,
                'success' => false,
                'error' => $e->getMessage()
            ], 200);
        }
    }
    
    // GET /api/country-sponsor-list/all
    public function countrySponsorListAll()
    {
        try {
            $sponsors = CountrySponsor::with('user')
                ->orderBy('updated_at', 'desc')
                ->get();
            
            $transformedSponsors = [];
            foreach ($sponsors as $sponsor) {
                $item = [
                    'id' => $sponsor->id,
                    'user_id' => $sponsor->user_id,
                    'country_id' => $sponsor->country_id,
                    'sponsor_type_id' => $sponsor->sponsor_type_id ?? 0,
                    'sponsor_tier_id' => $sponsor->sponsor_tier_id ?? 0,
                ];
                
                if ($sponsor->user) {
                    $item['name'] = $sponsor->user->name ?? 'Sponsor';
                    $item['email'] = $sponsor->user->email ?? '';
                    $item['avatar'] = $sponsor->user->avatar ?? '/assets/imgs/sponsor-placeholder.jpg';
                }
                
                $transformedSponsors[] = $item;
            }
            
            return response()->json([
                'data' => $transformedSponsors,
                'total' => count($transformedSponsors),
                'success' => true
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error in countrySponsorListAll: ' . $e->getMessage());
            
            return response()->json([
                'data' => [],
                'total' => 0,
                'success' => false,
                'error' => $e->getMessage()
            ], 200);
        }
    }

    /**
     * EVENTS ENDPOINTS
     */
    
    // GET /api/event-upcoming/list?offset=0&limit=10
    public function eventUpcomingList(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $country_id = $request->input('country_id', null);
        
        // Use date_start instead of event_date, and eager load country
        $query = Event::with('country')
            ->where('date_start', '>=', now()->format('Y-m-d'))
            ->orderBy('date_start', 'ASC');
        
        if ($country_id !== null) {
            $query->where('country_id', $country_id);
        }
        
        $total = $query->count();
        $events = $query->offset($offset)->limit($limit)->get();
        
        return response()->json([
            'data' => $events,
            'total' => $total,
            'offset' => (int)$offset,
            'limit' => (int)$limit,
            'success' => true
        ], $this->successStatus);
    }
    
    // GET /api/event-past/list?offset=0&limit=10
    public function eventPastList(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $country_id = $request->input('country_id', null);
        
        // Use date_start instead of event_date, and eager load country
        $query = Event::with('country')
            ->where('date_start', '<', now()->format('Y-m-d'))
            ->orderBy('date_start', 'DESC');
        
        if ($country_id !== null) {
            $query->where('country_id', $country_id);
        }
        
        $total = $query->count();
        $events = $query->offset($offset)->limit($limit)->get();
        
        return response()->json([
            'data' => $events,
            'total' => $total,
            'offset' => (int)$offset,
            'limit' => (int)$limit,
            'success' => true
        ], $this->successStatus);
    }
    
    // GET /api/event-detail/{id}
    public function eventDetail($id)
    {
        $event = Event::find($id);
        
        if (!$event) {
            return response()->json([
                'error' => true,
                'message' => 'Event not found'
            ], 404);
        }
        
        return response()->json([
            'data' => $event,
            'success' => true
        ], $this->successStatus);
    }

    /**
     * USERS ENDPOINTS
     */
    
    // GET /api/user-list/all?offset=0&limit=20
    public function userListAll(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);
        $search = $request->input('search', '');
        
        $query = User::select('users.*')
            ->orderBy('users.created_at', 'DESC');
        
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        }
        
        $total = $query->count();
        $users = $query->offset($offset)->limit($limit)->get();
        
        return response()->json([
            'data' => $users,
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'success' => true
        ], $this->successStatus);
    }
    
    // GET /api/user-detail/{id}
    public function userDetail($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'error' => true,
                'message' => 'User not found'
            ], 404);
        }
        
        return response()->json([
            'data' => $user,
            'success' => true
        ], $this->successStatus);
    }
    
    // GET /api/judge-list-all?offset=0&limit=20
    public function judgeListAll(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);
        
        $query = User::where('is_judge', 1)
            ->orderBy('victory_points', 'DESC');
        
        $total = $query->count();
        $judges = $query->offset($offset)->limit($limit)->get();
        
        return response()->json([
            'data' => $judges,
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'success' => true
        ], $this->successStatus);
    }

    /**
     * CARS ENDPOINTS
     */
    
    // GET /api/car-list/all?offset=0&limit=20
    public function carListAll(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);
        $search = $request->input('search', '');
        
        $query = Car::select('cars.*')
            ->orderBy('cars.created_at', 'DESC');
        
        if ($search) {
            $query->where('brand', 'like', '%' . $search . '%')
                ->orWhere('model', 'like', '%' . $search . '%');
        }
        
        $total = $query->count();
        $cars = $query->offset($offset)->limit($limit)->get();
        
        return response()->json([
            'data' => $cars,
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'success' => true
        ], $this->successStatus);
    }
    
    // GET /api/car-detail/{id}
    public function carDetail($id)
    {
        $car = Car::find($id);
        
        if (!$car) {
            return response()->json([
                'error' => true,
                'message' => 'Car not found'
            ], 404);
        }
        
        return response()->json([
            'data' => $car,
            'success' => true
        ], $this->successStatus);
    }

    /**
     * HEALTH CHECK
     */
    public function healthCheck()
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'Local API is running',
            'database' => 'carnew',
            'timestamp' => now()
        ], $this->successStatus);
    }
}
