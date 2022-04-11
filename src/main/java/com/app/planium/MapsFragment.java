package com.app.planium;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.text.Spannable;
import android.text.SpannableStringBuilder;
import android.util.Log;
import android.view.GestureDetector;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.ImageLoader;
import com.app.planium.adapter.PlanListAdapter;
import com.app.planium.app.App;
import com.app.planium.constants.Constants;
import com.app.planium.model.Plan;
import com.app.planium.util.CustomRequest;
import com.app.planium.util.Helper;
import com.balysv.materialripple.MaterialRippleLayout;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapView;
import com.google.android.gms.maps.MapsInitializer;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.mikhaellopez.circularimageview.CircularImageView;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Locale;
import java.util.Map;

import androidx.cardview.widget.CardView;
import androidx.core.widget.NestedScrollView;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

public class MapsFragment extends Fragment implements Constants, SwipeRefreshLayout.OnRefreshListener {

    private static final String STATE_LIST = "State Adapter Data";
    private CardView mSearchFriendsBox;
    private MaterialRippleLayout mSearchFriendsButton;
    private CircularImageView mSearchFriendsImage;
    private TextView mSearchFriendsTitle;
    FloatingActionButton mapaFabButton;

    private RecyclerView mRecyclerView;
    private NestedScrollView mNestedView;


    SwipeRefreshLayout mItemsContainer;

    private ArrayList<Plan> itemsList;
    private PlanListAdapter itemsAdapter;

    private long planId = 0;

    private int itemId = 0;
    private int arrayLength = 0;
    private Boolean loadingMore = false;
    private Boolean restore = false;
    private Boolean loaded = false;
    MapView mMapView;
    private GoogleMap googleMap;

    public MapsFragment() {
        // Required empty public constructor
    }

    public MapsFragment newInstance(Boolean pager) {

        MapsFragment myFragment = new MapsFragment();

        Bundle args = new Bundle();
        args.putBoolean("pager", pager);
        myFragment.setArguments(args);
        return myFragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);

        setHasOptionsMenu(false);

        if (savedInstanceState != null) {

            itemsList = savedInstanceState.getParcelableArrayList(STATE_LIST);
            itemsAdapter = new PlanListAdapter(getActivity(), itemsList);

            restore = savedInstanceState.getBoolean("restore");
            loaded = savedInstanceState.getBoolean("loaded");
            itemId = savedInstanceState.getInt("itemId");

        } else {

            itemsList = new ArrayList<Plan>();
            itemsAdapter = new PlanListAdapter(getActivity(), itemsList);

            restore = false;
            loaded = false;
            itemId = 0;
        }

        Intent i = getActivity().getIntent();

        planId = i.getLongExtra("planId", 0);
        if (planId == 0) planId = App.getInstance().getId();
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

        View rootView = inflater.inflate(R.layout.activity_maps3, container, false);
        mMapView = (MapView) rootView.findViewById(R.id.mapView);
        mMapView.onCreate(savedInstanceState);

        mMapView.onResume(); // needed to get the map to display immediately

        mItemsContainer = (SwipeRefreshLayout) rootView.findViewById(R.id.container_items);
        mItemsContainer.setOnRefreshListener(this);

        mapaFabButton = (FloatingActionButton) rootView.findViewById(R.id.fabbButton);
        mapaFabButton.setImageResource(R.drawable.ic_plus_mapa);
        mapaFabButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                Intent i = new Intent(getActivity(), NewPlanActivity.class);
                startActivity(i);
            }
        });

        //


        // Search Friends spotlight

        mSearchFriendsBox = (CardView) rootView.findViewById(R.id.searchFriendsBox);
        mSearchFriendsButton = (MaterialRippleLayout) rootView.findViewById(R.id.searchFriendsButton);

        if (!loaded) mSearchFriendsBox.setVisibility(View.GONE);

        mSearchFriendsButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                Intent intent = new Intent(getActivity(), NearbyActivity.class);
                startActivityForResult(intent, 1001);
            }
        });

        mSearchFriendsImage = (CircularImageView) rootView.findViewById(R.id.searchFriendsImage);
        mSearchFriendsTitle = (TextView) rootView.findViewById(R.id.searchFriendsTitle);

        //

        mNestedView = (NestedScrollView) rootView.findViewById(R.id.nested_view);

        mRecyclerView = (RecyclerView) rootView.findViewById(R.id.recycler_view);

        final LinearLayoutManager mLayoutManager = new GridLayoutManager(getActivity(), Helper.getGridSpanCount(getActivity()));
        mRecyclerView.setLayoutManager(mLayoutManager);
        mRecyclerView.setHasFixedSize(true);
        mRecyclerView.setItemAnimator(new DefaultItemAnimator());

        mRecyclerView.setAdapter(itemsAdapter);

        mRecyclerView.setNestedScrollingEnabled(false);


        mNestedView.setOnScrollChangeListener(new NestedScrollView.OnScrollChangeListener() {

            @Override
            public void onScrollChange(NestedScrollView v, int scrollX, int scrollY, int oldScrollX, int oldScrollY) {

                if (scrollY < oldScrollY) { // up


                }

                if (scrollY > oldScrollY) { // down


                }

                if (scrollY == (v.getChildAt(0).getMeasuredHeight() - v.getMeasuredHeight())) {

                    if (!loadingMore && !(mItemsContainer.isRefreshing())) {

                        mItemsContainer.setRefreshing(true);

                        loadingMore = true;

                        getItems();
                    }
                }
            }
        });

        mRecyclerView.addOnItemTouchListener(new RecyclerItemClickListener(getActivity(), mRecyclerView, new RecyclerItemClickListener.OnItemClickListener() {
            @Override
            public void onItemClick(View view, int position) {

                Plan item = (Plan) itemsList.get(position);

                Intent intent = new Intent(getActivity(), PlanActivity.class);
                intent.putExtra("planId", item.getId());
                startActivity(intent);
            }

            @Override
            public void onItemLongClick(View view, int position) {
                // ...
            }
        }));


        if (!restore && !loaded) {


            getItems();
        }
        try {
            MapsInitializer.initialize(getActivity().getApplicationContext());
        } catch (Exception e) {
            e.printStackTrace();
        }

        mMapView.getMapAsync(new OnMapReadyCallback() {
            @Override
            public void onMapReady(GoogleMap mMap) {
                googleMap = mMap;
                googleMap.setMyLocationEnabled(true);

                // For showing a move to my location button
                CustomRequest jsonReq = new CustomRequest(Request.Method.POST, METHOD_GET_PLANS, null,
                        new Response.Listener<JSONObject>() {
                            @Override
                            public void onResponse(JSONObject response) {

                                if (!isAdded() || getActivity() == null) {

                                    Log.e("ERROR", "PeopleNearbyFragment Not Added to Activity");

                                    return;
                                }

                                if (!loadingMore) {

                                    itemsList.clear();
                                }

                                try {

                                    arrayLength = 0;

                                    if (!response.getBoolean("error")) {

                                        itemId = response.getInt("itemId");

                                        if (response.has("items")) {

                                            JSONArray usersArray = response.getJSONArray("items");
                                            arrayLength = usersArray.length();
                                            //Log.e("IdBasedatos: ", String.valueOf(arrayLength));
                                            //Log.e("IdBasedatos: ", String.valueOf(usersArray));
                                            if (arrayLength > 0) {

                                                for (int i = 0; i < usersArray.length(); i++) {

                                                    JSONObject userObj = (JSONObject) usersArray.get(i);
                                                    //Log.e("LatLng: ", String.valueOf(userObj.get("lat"))+ ", "+String.valueOf(userObj.get("lng")));
                                                    Plan plan = new Plan(userObj);
                                                    itemsList.add(plan);
                                                    //Log.e("id perfil: ", String.valueOf(itemsList.getDistance()));
                                                    Log.e("distancia: ", Double.toString(plan.getDistance()));
                                                    LatLng mipos = new LatLng(plan.getLat(), plan.getLng());
                                                    mMap.addMarker(new MarkerOptions().position(mipos).title(plan.getUsername()).icon(BitmapDescriptorFactory.fromResource(R.drawable.ic_launcher_planeator_45)));
                                                }
                                            }
                                        }

                                    }

                                } catch (JSONException e) {

                                    e.printStackTrace();

                                } finally {

                                    if (!isAdded() || getActivity() == null) {

                                        Log.e("ERROR", "PeopleNearbyFragment Not Added to Activity");

                                        return;
                                    }


                                    Log.e("Response", response.toString());
                                }
                            }
                        }, new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {

                    }
                }) {

                    @Override
                    protected Map<String, String> getParams() {
                        Map<String, String> params = new HashMap<String, String>();
                        params.put("accountId", Long.toString(App.getInstance().getId()));
                        params.put("accessToken", App.getInstance().getAccessToken());
                        params.put("itemId", Long.toString(itemId));
                        params.put("lat", Double.toString(App.getInstance().getLat()));
                        params.put("lng", Double.toString(App.getInstance().getLng()));
                        return params;
                    }
                };

                App.getInstance().addToRequestQueue(jsonReq);
                //


                LatLng mipos = new LatLng(App.getInstance().getLat(), App.getInstance().getLng());
                //mMap.addMarker(new MarkerOptions().position(mipos).title("Aquí estoy"));
                mMap.moveCamera(CameraUpdateFactory.newLatLngZoom(mipos,8));
            }
        });


        // Inflate the layout for this fragment
        return rootView;
    }

    @Override
    public void setUserVisibleHint(boolean isVisibleToUser) {

        super.setUserVisibleHint(isVisibleToUser);

        if (isVisibleToUser) {

            if (loaded) updateProfileInfo();

            Handler handler = new Handler();

            handler.postDelayed(new Runnable() {
                @Override
                public void run() {

                    if (isAdded()) {

                        if (!loaded) {


                            getItems();

                        } else {

                            if (App.getInstance().getNewFriendsCount() > 0) {

                                loaded = false;


                                mSearchFriendsBox.setVisibility(View.GONE);

                                itemId = 0;

                                getItems();
                            }
                        }
                    }
                }
            }, 50);
        }
    }

    private void updateProfileInfo() {

        if (isAdded()) {

            if (planId == 0 || App.getInstance().getId() == planId) {

                if (isAdded()) {

                    mSearchFriendsBox.setVisibility(View.VISIBLE);

                    if (App.getInstance().getPhotoUrl() != null && App.getInstance().getPhotoUrl().length() > 0) {

                        App.getInstance().getImageLoader().get(App.getInstance().getPhotoUrl(), ImageLoader.getImageListener(mSearchFriendsImage, R.drawable.profile_default_photo, R.drawable.profile_default_photo));

                    } else {

                        mSearchFriendsImage.setImageResource(R.drawable.profile_default_photo);
                    }

                    if (App.getInstance().getPhotoUrl() != null && App.getInstance().getPhotoUrl().length() > 0) {

                        App.getInstance().getImageLoader().get(App.getInstance().getPhotoUrl(), ImageLoader.getImageListener(mSearchFriendsImage, R.drawable.profile_default_photo, R.drawable.profile_default_photo));

                    } else {

                        mSearchFriendsImage.setImageResource(R.drawable.profile_default_photo);
                    }

                    SpannableStringBuilder txt = new SpannableStringBuilder(String.format(Locale.getDefault(), getString(R.string.msg_search_friends_promo), App.getInstance().getFullname()));
                    txt.setSpan(new android.text.style.StyleSpan(android.graphics.Typeface.BOLD), 0, App.getInstance().getFullname().length() - 1, Spannable.SPAN_EXCLUSIVE_EXCLUSIVE);

                    mSearchFriendsTitle.setText(txt);
                }

            } else {

                mSearchFriendsBox.setVisibility(View.GONE);
            }
        }
    }

    @Override
    public void onRefresh() {

        if (App.getInstance().isConnected()) {

            itemId = 0;
            getItems();

        } else {

            mItemsContainer.setRefreshing(false);
        }
    }

    @Override
    public void onSaveInstanceState(Bundle outState) {

        super.onSaveInstanceState(outState);

        outState.putBoolean("restore", true);
        outState.putBoolean("loaded", loaded);
        outState.putInt("itemId", itemId);
        outState.putParcelableArrayList(STATE_LIST, itemsList);
    }

    @Override
    public void onCreateOptionsMenu(Menu menu, MenuInflater inflater) {

        super.onCreateOptionsMenu(menu, inflater);
    }


    public void getItems() {

        mItemsContainer.setRefreshing(true);

        CustomRequest jsonReq = new CustomRequest(Request.Method.POST, METHOD_FRIENDS_GET, null,
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {

                        if (!isAdded() || getActivity() == null) {

                            Log.e("ERROR", "Friends Fragment Not Added to Activity");

                            return;
                        }

                        if (!loadingMore) {

                            itemsList.clear();
                        }

                        try {

                            arrayLength = 0;

                            if (!response.getBoolean("error")) {

                                if (itemId == 0 && App.getInstance().getId() == planId) {

                                    App.getInstance().setNewFriendsCount(0);
                                }

                                itemId = response.getInt("itemId");

                                if (response.has("items")) {

                                    JSONArray usersArray = response.getJSONArray("items");

                                    arrayLength = usersArray.length();

                                    if (arrayLength > 0) {

                                        for (int i = 0; i < usersArray.length(); i++) {

                                            JSONObject userObj = (JSONObject) usersArray.get(i);

                                            Plan item = new Plan(userObj);

                                            itemsList.add(item);
                                        }
                                    }
                                }

                            }

                        } catch (JSONException e) {

                            e.printStackTrace();

                        } finally {

                            Log.d("Friends", response.toString());

                            loadingComplete();
                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {

                if (!isAdded() || getActivity() == null) {

                    Log.e("ERROR", "Friends Fragment Not Added to Activity");

                    return;
                }

                loadingComplete();
            }
        }) {

            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<String, String>();
                params.put("accountId", Long.toString(App.getInstance().getId()));
                params.put("accessToken", App.getInstance().getAccessToken());
                params.put("planId", Long.toString(planId));
                params.put("itemId", Long.toString(itemId));
                params.put("language", "en");

                return params;
            }
        };

        App.getInstance().addToRequestQueue(jsonReq);
    }

    public void loadingComplete() {

        loaded = true;

        updateProfileInfo();


        itemsAdapter.notifyDataSetChanged();


        loadingMore = false;
        mItemsContainer.setRefreshing(false);
    }

    static class RecyclerItemClickListener implements RecyclerView.OnItemTouchListener {

        public interface OnItemClickListener {

            void onItemClick(View view, int position);

            void onItemLongClick(View view, int position);
        }

        private OnItemClickListener mListener;

        private GestureDetector mGestureDetector;

        public RecyclerItemClickListener(Context context, final RecyclerView recyclerView, OnItemClickListener listener) {

            mListener = listener;

            mGestureDetector = new GestureDetector(context, new GestureDetector.SimpleOnGestureListener() {
                @Override
                public boolean onSingleTapUp(MotionEvent e) {

                    return true;
                }

                @Override
                public void onLongPress(MotionEvent e) {

                    View childView = recyclerView.findChildViewUnder(e.getX(), e.getY());

                    if (childView != null && mListener != null) {

                        mListener.onItemLongClick(childView, recyclerView.getChildAdapterPosition(childView));
                    }
                }
            });
        }

        @Override
        public boolean onInterceptTouchEvent(RecyclerView view, MotionEvent e) {

            View childView = view.findChildViewUnder(e.getX(), e.getY());

            if (childView != null && mListener != null && mGestureDetector.onTouchEvent(e)) {

                mListener.onItemClick(childView, view.getChildAdapterPosition(childView));
            }

            return false;
        }

        @Override
        public void onTouchEvent(RecyclerView view, MotionEvent motionEvent) {

        }

        @Override
        public void onRequestDisallowInterceptTouchEvent(boolean disallowIntercept) {

        }
    }


    @Override
    public void onAttach(Activity activity) {
        super.onAttach(activity);
    }

    @Override
    public void onDetach() {
        super.onDetach();
    }
}