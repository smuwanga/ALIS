@section("sidebar")
	<nav id="side_nav">
			<ul>

				<li>
					<a href="{{ URL::route('user.home')}}"><span class="ion-home"></span> <span class="nav_title">Main Menu</span></a>
				</li>

				<li class="nav_trigger">
					<a href="#">
						<span class="ion-stats-bars"></span>
						<span class="nav_title">Reports</span>
					</a>
					<div class="sub_panel" style="left: -220px;">
						<div class="side_inner ps-ready ps-container" style="height: 620px;">
							<h4 class="panel_heading panel_heading_first">{{trans('messages.daily-reports')}}</h4>
							<ul>
								<li>
									<div>
										<a href="{{ URL::route('reports.patient.index')}}">
											<span class="glyphicon glyphicon-tag"></span>
											{{trans('messages.patient-report')}}</a>
									</div>
									<div>
										<a href="{{ URL::route('reports.patient.merged')}}">
											<span class="glyphicon glyphicon-tag"></span>
											{{trans('Patient Report')}}
										</a>
									</div>
								</li>
								<li>
									<div><a href="{{ URL::route('reports.daily.log')}}">
										<span class="glyphicon glyphicon-tag"></span>
										{{trans('messages.daily-log')}}</a>
									</div>
								</li>
								<li>
									<div><a href="{{ URL::route('reports.microbiology.search')}}">
										<span class="glyphicon glyphicon-tag"></span>
										Microbiology Export</a>
									</div>
								</li>
							</ul>
							<h4 class="panel_heading panel_heading_first">{{trans('messages.aggregate-reports')}}</h4>
							<ul>
								<li>
									<div><a href="{{ URL::route('reports.aggregate.prevalence')}}">
										<span class="glyphicon glyphicon-tag"></span>
										{{trans('messages.positivity-rates')}}</a>
									</div>
								</li>
								<li>
									<div><a href="{{ URL::route('reports.aggregate.surveillance')}}">
										<span class="glyphicon glyphicon-tag"></span>
										{{trans('messages.surveillance')}}</a>
									</div>
								</li>
								<li>
									<div><a href="{{ URL::route('reports.aggregate.counts')}}">
										<span class="glyphicon glyphicon-tag"></span>
										{{trans('messages.counts')}}</a>
									</div>
								</li>
								<li>
									<div><a href="{{ URL::route('reports.aggregate.tat')}}">
										<span class="glyphicon glyphicon-tag"></span>
										{{trans('messages.turnaround-time')}}</a>
									</div>
								</li>
								<li>
									<div><a href="{{ URL::route('reports.aggregate.infection')}}">
										<span class="glyphicon glyphicon-tag"></span>
										{{trans('messages.infection-report')}}</a>
									</div>
								</li>
								<li>
									<div><a href="{{ URL::route('reports.aggregate.userStatistics')}}">
										<span class="glyphicon glyphicon-tag"></span>
										{{trans('messages.user-statistics-report')}}</a>
									</div>
								</li>
								<li class="hidden">
									<div><a href="{{ URL::route('reports.aggregate.hmis105')}}">
										<span class="glyphicon glyphicon-tag"></span>
										HMIS 105</a>
									</div>
								</li>
								<li>
									<div><a href="{{ URL::route('report.hmis105')}}">
										<span class="glyphicon glyphicon-tag"></span>
										HMIS 105</a>
									</div>
								</li>
<!-- 								<li>
									<div><a href="{{ URL::route('reports.aggregate.moh706')}}">
										<span class="glyphicon glyphicon-tag"></span>
										{{trans('messages.moh-706')}}</a>
									</div>
								</li>
 								<li>
									<div><a href="{{ URL::route('reports.aggregate.cd4')}}">
										<span class="glyphicon glyphicon-tag"></span>
										{{trans('messages.cd4-report')}}</a>
									</div>
								</li>
								<li>
									<div><a href="{{ URL::route('reports.qualityControl')}}">
										<span class="glyphicon glyphicon-tag"></span>
										{{Lang::choice('messages.quality-control', 2)}}</a>
									</div>
								</li>
 -->
							</ul>
							<h4 class="panel_heading panel_heading_first">{{trans('messages.inventory-reports')}}</h4>
							<ul>
								<li>
									<div><a href="{{ URL::route('reports.inventory')}}">
										<span class="glyphicon glyphicon-tag"></span>
										{{trans('messages.stock-levels')}}</a>
									</div>
								</li>
							</ul>
							<h4 class="panel_heading panel_heading_first">Dashboard</h4>
							<ul>
								<li>
									<div><a href="{{ URL::route('dashboard.index')}}">
										<span class="glyphicon glyphicon-tag"></span>
										Dashboard</a>
									</div>
								</li>
							</ul>

						<div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px; width: 215px; display: none;"><div class="ps-scrollbar-x" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px; height: 620px; display: none;"><div class="ps-scrollbar-y" style="top: 0px; height: 0px;"></div></div></div>
					</div>
				</li>

				<li class="nav_trigger">
					<a href="#"><span class="ion-person"></span><span class="nav_title">Patient Information</span>
					</a>
					<div class="sub_panel" style="left: -220px;">
						<div class="side_inner ps-ready ps-container" style="height: 620px;">
							<h4 class="panel_heading panel_heading_first">{{ Lang::choice('messages.patient-unhls', 1)}}</h4>
							<ul>
								<li>
									<div>
										<a href="{{ URL::route('unhls_patient.create')}}">
											<span class="glyphicon glyphicon-tag"></span> {{Lang::choice('messages.register-new-patient', 1)}}</a>
									</div>
								</li>
								<li>
									<div>
										<a href="{{ URL::route('poc.create') }}">
											<span class="glyphicon glyphicon-tag"></span> {{"Register EID patient"}}</a>
									</div>
								</li>

								<li>
									<div>
										<a href="{{ URL::route('microbio.create') }}">
											<span class="glyphicon glyphicon-tag"></span> {{ 'Microbiology Specimen' }}</a>
									</div>
								</li>
								<li>
									<div>
										<a href="{{ URL::route('unhls_patient.index') }}">
											<span class="glyphicon glyphicon-tag"></span> {{Lang::choice('messages.view-patients', 1)}}</a>
									</div>
								</li>

							</ul>

						<div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px; width: 215px; display: none;"><div class="ps-scrollbar-x" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px; height: 620px; display: none;"><div class="ps-scrollbar-y" style="top: 0px; height: 0px;"></div></div></div>
					</div>
				</li>
				<li>
					<a href="{{ URL::route('visit.index') }}"><span class="ion-person"></span><span class="nav_title">Visits</span>
					</a>
				</li>
				<li class="nav_trigger">
					<a href="#">
						<span class="ion-erlenmeyer-flask"></span>
						<span class="nav_title">Tests</span>
					</a>
					<div class="sub_panel" style="left: -220px;">
						<div class="side_inner ps-ready ps-container" style="height: 620px;">
							<h4 class="panel_heading panel_heading_first"> Tests</h4>
							<ul>
								<li>
									<div>
										<a href="javascript:void(0)" data-toggle="modal" data-target="#new-test-modal-unhls">
											<!--<span class="glyphicon glyphicon-plus-sign"></span>{{trans('messages.new-test')}}-->
											<span class="glyphicon glyphicon-plus-sign"></span> Make Test Request
										</a>
									</div>
								</li>

								<li>
									<div>
										<a href="{{ URL::route('unhls_test.index')}}">
											<!--<span class="glyphicon glyphicon-tag"></span> {{Lang::choice('messages.test-unhls', 2)}}</a>-->
											<span class="glyphicon glyphicon-tag"></span> List of All Tests</a>
									</div>
								</li>
								<li>
									<div>
										<a href="{{URL::route('test.completed')}}">
											<span class="glyphicon glyphicon-tag" ></span>{{trans('Completed Tests')}}
										</a>
									</div>
								</li>
																<li>
									<div>
										<a href="{{URL::route('test.notrecieved')}}">
											<span class="glyphicon glyphicon-tag" ></span>{{trans('Samples Not Recieved')}}
										</a>
									</div>
								</li>
																<li>
									<div>
										<a href="{{URL::route('test.pending')}}">
											<span class="glyphicon glyphicon-tag" ></span>{{trans('Pending Tests')}}
										</a>
									</div>
								</li>
																<li>
									<div>
										<a href="{{URL::route('test.started')}}">
											<span class="glyphicon glyphicon-tag" ></span>{{trans('Tests Started')}}
										</a>
									</div>
								</li>
								<li>
									<div>
										<a href="{{URL::route('test.verified')}}">
											<span class="glyphicon glyphicon-tag" ></span>Reviewed Tests
										</a>
									</div>
								</li>
								<li>
									<div>
										<a href="{{URL::route('unhls_test.importPoc')}}">
											<span class="glyphicon glyphicon-tag" ></span>{{trans('Import POC results')}}
										</a>
									</div>
								</li>

								<li>
									<div>
										<a href="{{URL::route('poc.download')}}">
											<span class="glyphicon glyphicon-tag" ></span>{{trans('Download POC Data')}}
										</a>
									</div>
								</li>

							</ul>

						<div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px; width: 215px; display: none;"><div class="ps-scrollbar-x" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px; height: 620px; display: none;"><div class="ps-scrollbar-y" style="top: 0px; height: 0px;"></div></div></div>
					</div>
				</li>
			@if(Entrust::can('manage_lab_configurations'))

				<li class="nav_trigger">
					<a href="#">
					<span class="ion-wrench"></span>
						<span class="nav_title">Lab Configuration</>
					</a>
					<div class="sub_panel" style="left: -220px;">
						<div class="side_inner ps-ready ps-container" style="height:620px;">
							<h4 class="panel_heading panel_heading_first">Lab Configuration</h4>
							<ul>
								<li>
									<a href="{{URL::route("clinicians.index")}}">
									<span class="glyphicon glyphicon-tag"></span> Clinicians</a>
								</li>
								<li>
									<a href="{{URL::route("ward.index")}}">
									<span class="glyphicon glyphicon-tag"></span> Health Units</a>
								</li>
								<!-- <li>
									<a href="{{URL::route("referral.index")}}">
									<span class="glyphicon glyphicon-tag"></span> Manage Referrals</a>
								</li> -->
								<li>
									<a href="{{URL::route("instrument.index")}}">
									<span class="glyphicon glyphicon-tag"></span> {{trans('messages.instrument')}}</a>
								</li>
								<li>
									<a href="{{URL::route("reportconfig.surveillance")}}">
									<span class="glyphicon glyphicon-tag"></span> {{trans('messages.surveillance')}}</a>
								</li>
								<li>
									<a href="{{URL::route("barcode.index")}}">
									<span class="glyphicon glyphicon-tag"></span> {{trans('messages.barcode-settings')}}</a>
								</li>
								<li>
									<a href="{{ URL::route("blisclient.index") }}">
									<span class="glyphicon glyphicon-tag"></span>{{ trans('messages.interfaced-equipment')}}</a>
								</li>
								<li>
									<a href="{{ URL::route("testnamemapping.index") }}">
									<span class="glyphicon glyphicon-tag"></span>Test name Mapping</a>
								</li>
								<li>
									<a href="{{ URL::route("resetulin.create") }}">
									<span class="glyphicon glyphicon-tag"></span>{{'Reset ULIN' }}</a>
								</li>
							</ul>
						</div>
					</div>
				</li>
			@endif

			@if(Entrust::can('manage_test_catalog'))
				<li class="nav_trigger">
					<a href="#">
					<span class="ion-gear-a"></span>
						<span class="nav_title">Test Catalog</>
					</a>
					<div class="sub_panel" style="left: -220px;">
						<div class="side_inner ps-ready ps-container" style="height:620px;">
							<h4 class="panel_heading panel_heading_first">Test Catalog</h4>
							<ul>
								<li>
									<a href="{{URL::route("testcategory.index")}}">
									<span class="glyphicon glyphicon-tag"></span>Lab Sections</a>
								</li>

								<li>
									<a href="{{URL::route("specimentype.index")}}">
									<span class="glyphicon glyphicon-tag"></span>Specimen Types</a>
								</li>
								<li>
									<a href="{{URL::route("specimenrejection.index")}}">
									<span class="glyphicon glyphicon-tag"></span>Specimen Rejection</a>
								</li>
								<li>
									<a href="{{URL::route("testtype.index")}}">
									<span class="glyphicon glyphicon-tag"></span>Test Types</a>
								</li>
								<li>
									<a href="{{URL::route("drug.index")}}">
									<span class="glyphicon glyphicon-tag"></span>Drugs</a>
								</li>
								<li>
									<a href="{{URL::route("organism.index")}}">
									<span class="glyphicon glyphicon-tag"></span>Organisms</a>
								</li>
								<li>
									<a href="{{URL::route("testpurpose.index")}}">
									<span class="glyphicon glyphicon-tag"></span>Testing Purpose</a>
								</li>
							</ul>
						</div>
					</div>
				</li>
			@endif


			@if(Entrust::can('manage_inventory'))
				<li class="nav_trigger">
					<a href="#">
					<span class="ion-ios-cart"></span>
						<span class="nav_title">Inventory & Equipment</span>
					</a>
					<div class="sub_panel" style="left: -220px;">
						<div class="side_inner ps-ready ps-container" style="height: 620px;">
							<h4 class="panel_heading panel_heading_first">Inventory</h4>
							<ul>
								<li>
									<a href="{{ URL::route("item.index")}}">
									<span class="glyphicon glyphicon-tag"></span> {{trans('messages.stock-card')}}</a>
								</li>
								<li>

									<a href="{{ URL::route("request.index")}}">
									<span class="glyphicon glyphicon-tag"></span>Request For Commodities</a>
								</li>
							<li>
								<a href="{{ URL::route("reports.inventory")}}">
								<span class="glyphicon glyphicon-tag"></span> Stockbook</a>
							</li>
							<li>
								<a href="{{ URL::route("item.index")}}">
								<span class="glyphicon glyphicon-tag"></span> {{trans('messages.commodities')}}</a>
							</li>
							<li>
								<a href="{{ URL::route("supplier.index")}}">

								<span class="glyphicon glyphicon-tag"></span> {{Lang::choice('messages.suppliers',2)}}</a>
							</li>
							<!-- <li>
								<a href="{{ URL::route("reports.inventory")}}">
								<span class="glyphicon glyphicon-tag"></span> {{trans('messages.metrics')}}</a>
							</li> -->
							</ul>
							<h4 class="panel_heading">Equipment</h4>
							<ul>
								<li><a href="{{ URL::route("equipmentinventory.index")}}"><span class="glyphicon glyphicon-tag"></span> Inventory</a></li>
								<li><a href="{{ URL::route("equipmentmaintenance.index")}}"><span class="glyphicon glyphicon-tag"></span> Maintenance log</a></li>
								<li><a href="{{ URL::route("equipmentbreakdown.index")}}"><span class="glyphicon glyphicon-tag"></span> Breakdown</a></li>
								<li><a href="{{ URL::route("equipmentsupplier.index")}}"><span class="glyphicon glyphicon-tag"></span> Supplier</a></li>
							</ul>
						<div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px; width: 215px; display: none;"><div class="ps-scrollbar-x" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px; height: 620px; display: none;"><div class="ps-scrollbar-y" style="top: 0px; height: 0px;"></div></div></div>
					</div>
				</li>
			@endif


				<li class="nav_trigger">
					<a href="#">
						<span class="ion-nuclear"></span>
						<span class="nav_title">Biosafety & Biosecurity</span>
					</a>
					<div class="sub_panel" style="left: -220px;">
						<div class="side_inner ps-ready ps-container" style="height: 620px;">
							<h4 class="panel_heading panel_heading_first">Bio-safety / Bio-security</h4>
							<ul>
							<li>
								<a href="{{ URL::route('bbincidence.index')}}">
								<span class="glyphicon glyphicon-list"></span> Summary Log</a>
							</li>
							<li>
							<a href="{{ URL::route('bbincidence.create')}}">
								<span class="glyphicon glyphicon-plus-sign"></span> Register Incident</a>
							</li>
							<li>
							<a href="{{ URL::route('bbincidence.bbfacilityreport')}}">
								<span class="glyphicon glyphicon-stats"></span> Facility Report</a>
							</li>
							</ul>

						<div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px; width: 215px; display: none;"><div class="ps-scrollbar-x" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px; height: 620px; display: none;"><div class="ps-scrollbar-y" style="top: 0px; height: 0px;"></div></div></div>
					</div>
				</li>

			@if(Entrust::can('manage_users'))

				<li class="nav_trigger">
					<a href="#">
						<span class="ion-key"></span>
						<span class="nav_title">Access Control</span>
					</a>
					<div class="sub_panel" style="left: -220px;">
						<div class="side_inner ps-ready ps-container" style="height: 620px;">
							<h4 class="panel_heading panel_heading_first">Access Control</h4>
							<ul>
					<li>
						<div>
							<a href="{{ (Entrust::can('manage_users')) ? URL::route('user.index') : URL::to('user/'.Auth::user()->id.'/edit') }}">
								<span class="glyphicon glyphicon-tag"></span> {{trans('messages.user-accounts')}}</a>
						</div>
					</li>
					<li>
						<div>
							<a href="{{ URL::route("permission.index")}}">
								<span class="glyphicon glyphicon-tag"></span> {{ Lang::choice('messages.permission', 2)}}</a>
						</div>
					</li>
					<li>
						<div>
							<a href="{{ URL::route("role.index")}}">
								<span class="glyphicon glyphicon-tag"></span> {{ Lang::choice('messages.role', 2)}}</a>
						</div>
					</li>
					<li>
						<div>
							<a href="{{ URL::route("role.assign")}}">
								<span class="glyphicon glyphicon-tag"></span> {{trans('messages.assign-roles')}}</a>
						</div>
					</li>
							</ul>

						<div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px; width: 215px; display: none;"><div class="ps-scrollbar-x" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px; height: 620px; display: none;"><div class="ps-scrollbar-y" style="top: 0px; height: 0px;"></div></div></div>
					</div>
				</li>
			@endif

				<li class="nav_trigger">
					<a href="#">
						<span class="ion-ios-folder"></span>
						<span class="nav_title">Other Resouces</span>
					</a>
					<div class="sub_panel" style="left: -220px;">
						<div class="side_inner ps-ready ps-container" style="height: 620px;">
							<h4 class="panel_heading panel_heading_first">Other Resources</h4>
							<ul>
							<li>
								<a href="http://www.cphluganda.org/" target="_blank">
								<span class=""></span> EID Dashboard</a>
							</li>
							<li>
							<a href="http://vldash.cphluganda.org/" target="_blank">
								<span class=""></span> Viral Load Dashboard</a>
							</li>
							<li>
							<a href="http://cphl.go.ug/" target="_blank">
								<span class=""></span> CPHL/UNHLS Website</a>
							</li>
							<li>
							<a href="http://health.go.ug/" target="_blank">
								<span class=""></span> MoH Website</a>
							</li>
							</ul>

						<div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px; width: 215px; display: none;"><div class="ps-scrollbar-x" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px; height: 620px; display: none;"><div class="ps-scrollbar-y" style="top: 0px; height: 0px;"></div></div></div>
					</div>
				</li>

			</ul>
		</nav>
@show
